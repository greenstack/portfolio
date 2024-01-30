namespace Greenstack.Emerald.Tests;

using Greenstack.Emerald.Containers;

[TestFixture]
public class DeltaQueueTests
{
    [Test]
    public void TestEnqueueAndTick()
    {
        DeltaQueue<int> queue = new();

        var dequeued = queue.Tick(10);
        Assert.That(dequeued, Has.Count.EqualTo(0));

        queue.Enqueue(1, 1);
        Assert.Multiple(() =>
        {
            Assert.That(queue.Peek(), Is.EqualTo(1));
            Assert.That(queue.TimeUntilNextRelease, Is.EqualTo(1));
        });

        queue.Enqueue(2, .5);
        Assert.Multiple(() =>
        {
            Assert.That(queue.Count, Is.EqualTo(2));
            Assert.That(queue.Peek(), Is.EqualTo(2));
            Assert.That(queue.TimeUntilNextRelease, Is.EqualTo(.5));
        });

        dequeued = queue.Tick(.5);
        Assert.Multiple(() =>
        {
            Assert.That(queue.Count, Is.EqualTo(1));
            Assert.That(dequeued, Has.Count.EqualTo(1));
            Assert.That(dequeued[0], Is.EqualTo(2));
            Assert.That(queue.TimeUntilNextRelease, Is.EqualTo(.5));
            Assert.That(queue.Peek(), Is.EqualTo(1));
        });

        // Should be released in 1 second, .5 seconds after the next element
        queue.Enqueue(3, 1);
        // Should be released in .75 seconds, .25 seconds after the next element
        queue.Enqueue(4, .75);
        dequeued = queue.Tick(.75);
        Assert.Multiple(() => 
        {
            Assert.That(dequeued, Has.Count.EqualTo(2));
            // First item should be 1
            Assert.That(dequeued[0], Is.EqualTo(1));
            // Second item should be 4
            Assert.That(dequeued[1], Is.EqualTo(4));
            Assert.That(queue.Peek(), Is.EqualTo(3));
        });
    }
}