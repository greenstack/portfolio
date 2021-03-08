using UnityEngine;
using UnityEngine.UI;

/// <summary>
/// Controls the PlayerTimer's view.
/// </summary>
public class TimerDisplayController : MonoBehaviour
{
    [SerializeField]
    private AudioClip runningOutOfTimeNoise = default;
    [SerializeField]
    private AudioClip outOfTimeNoise = default;

    [SerializeField]
    private Text display = default;

    [SerializeField]
    [Tooltip("The color that's used to show the player that they're running out of time.")]
    private Color dangerColor = Color.red;

    private Color defaultColor;

    [SerializeField]
    [Tooltip("At what point should the timer start flashing the Danger Color to notify the player they're running out of time?")]
    private int dangerTime = 30;

    private void Start()
    {
        defaultColor = display.color;
    }

    /// <summary>
    /// Hook this up with a PlayerTimer's <see cref="PlayerTimer.OnTimeRemainingChanged"/> event. This will update the display with the current amount of time remaining.
    /// </summary>
    /// <param name="newTime">The new amount of time remaining.</param>
    public void OnTimerChanged(float newTime)
    {
        int minutes = (int)newTime / MasterTimer.SECONDS_PER_MINUTE;
        int seconds = (int)newTime % MasterTimer.SECONDS_PER_MINUTE;

        display.text = $"{minutes:D2}:{seconds:D2}";

        if (newTime < dangerTime)
        {
            display.color = newTime % 2 == 0 ? dangerColor : defaultColor;
            if (runningOutOfTimeNoise != null && newTime < 10)
            {
                AudioSource.PlayClipAtPoint(runningOutOfTimeNoise, Vector3.zero);
            }
        }
        else
        {
            display.color = defaultColor;
        }
    }

    /// <summary>
    /// Plays the <see cref="outOfTimeNoise"/> when the player is out of time.
    /// 
    /// Hook this up with <see cref="PlayerTimer.OnTimeOut"/>.
    /// </summary>
    public void OutOfTime()
    {
        AudioSource.PlayClipAtPoint(outOfTimeNoise, Vector3.zero);
    }
}
