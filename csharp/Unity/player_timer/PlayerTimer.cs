using UnityEngine;
using UnityEngine.Events;

/// <summary>
/// Represents a player's available time.
/// </summary>
public class PlayerTimer : MonoBehaviour
{
    [SerializeField]
    private float secondsRemaining = default;

    /// <summary>
    /// Gets the raw number of seconds remaining for the player.
    /// </summary>
    public float SecondsRemaining
    {
        get => secondsRemaining;
        private set
        {
            secondsRemaining = value;
            OnTimeRemainingChanged?.Invoke(secondsRemaining);
            if (secondsRemaining <= 0)
            {
                OnTimeOut?.Invoke();
                CancelInvoke(nameof(Tick));
            }
        }
    }

    [System.Serializable]
    public class TimeOutEvent : UnityEvent { }
    [Tooltip("Fired whenever the time reaches zero.")]
    public TimeOutEvent OnTimeOut;

    [System.Serializable]
    public class TimeRemainingChangedEvent : UnityEvent<float> { }
    [Tooltip("Fired whenever the time is changed.")]
    public TimeRemainingChangedEvent OnTimeRemainingChanged;

    [System.Serializable]
    public class TimerStartedEvent : UnityEvent { }
    [Tooltip("Add a listener when you need something to react to the timer starting.")]
    public TimerStartedEvent OnTimerStarted;

    private bool paused;

    /// <summary>
    /// Causes the player time to begin ticking from the start.
    /// </summary>
    /// <param name="startingTime">The amount of time that should be allotted to the player.</param>
    public void StartTicking(float startingTime)
    {
        SecondsRemaining = startingTime;
        OnTimerStarted?.Invoke();
        InvokeRepeating(nameof(Tick), 1, 1);
    }

    /// <summary>
    /// Adds the given number of seconds to the player.
    /// </summary>
    /// <param name="seconds">The amount of time to give the player.</param>
    public void AddTime(int seconds)
    {
        SecondsRemaining += seconds;
    }

    /// <summary>
    /// Removes a second from the player's available time.
    /// </summary>
    private void Tick()
    {
        SecondsRemaining--;
    }

    /// <summary>
    /// Toggles the timer's pause state.
    /// </summary>
    public void TogglePause()
    {
        if (paused) Unpause();
        else Pause();
    }

    /// <summary>
    /// Pauses the timer, which prevents it from ticking.
    /// </summary>
    public void Pause()
    {
        if (paused) return;
        paused = true;
        CancelInvoke(nameof(Tick));
    }

    /// <summary>
    /// Unpauses the timer, which causes it to start ticking.
    /// </summary>
    public void Unpause()
    {
        if (!paused) return;
        paused = false;
        InvokeRepeating(nameof(Tick), 1, 1);
    }
}
