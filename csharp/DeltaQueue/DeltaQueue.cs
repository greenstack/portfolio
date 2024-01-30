using System.Collections.Generic;

namespace Greenstack.Emerald.Containers;

/// <summary>
/// Stores items based on a delta between them.
/// </summary>
/// <typeparam name="T">The type of item to store.</typeparam>
public class DeltaQueue<T>
{
	private class Item
	{
		public T Value;
		public double TimeTilRelease;
	}

	/// <summary>
	/// The number of items in the queue.
	/// </summary>
	public int Count => _internalQueue.Count;

	private readonly LinkedList<Item> _internalQueue = new();

	/// <summary>
	/// Adds an item to the queue.
	/// </summary>
	/// <param name="item">The item to add to the queue.</param>
	/// <param name="time">How much time should elapse before the item is released from the queue.</param>
	public void Enqueue(T item, double time)
    {
        if (time < 0)
        {
            throw new System.ArgumentOutOfRangeException(nameof(time), "Time must be >= 0");
        }

        LinkedListNode<Item> insertBefore = _internalQueue.First;

        foreach (Item i in _internalQueue)
        {
            if (i.TimeTilRelease < time)
            {
                time -= i.TimeTilRelease;
                insertBefore = insertBefore.Next;
            }
            else
            {
                _internalQueue.First.Value.TimeTilRelease -= time;
            }
        }

        var node = new LinkedListNode<Item>(new Item { Value = item, TimeTilRelease = time, });
        if (insertBefore == null)
        {
            _internalQueue.AddLast(node);
        }
        else
        {
            _internalQueue.AddBefore(insertBefore, node);
        }
    }

	/// <summary>
	/// Returns the first element of the queue.
	/// </summary>
	public T Peek()
    {
        return _internalQueue.First.Value.Value;
    }

	/// <summary>
	/// The amount of time that must elapse before the next release.
	/// </summary>
	public double TimeUntilNextRelease =>
        _internalQueue.First.Value.TimeTilRelease;

	/// <summary>
	/// Updates the amount of time elapsed.
	/// </summary>
	/// <param name="elapsed">The amount of time that has elapsed.</param>
	/// <returns>A list of objects that will be released during the update.</returns>
	public List<T> Tick(double deltaTime)
    {
        List<T> removedItems = new();
        double decrement = deltaTime;
        while(_internalQueue.First != null) {
            _internalQueue.First.Value.TimeTilRelease -= decrement;
            
            if (_internalQueue.First.Value.TimeTilRelease <= 0)
            {
                Item i = _internalQueue.First.Value;
                _internalQueue.RemoveFirst();
                removedItems.Add(i.Value);
                decrement = -i.TimeTilRelease;
            }
            else
                break;
        }

        return removedItems;
    }

}
