namespace DualResonance.Deployables.Units
{
    /// <summary>
    /// Helps maintain the state of a Mercenary.
    /// </summary>
    public interface IUnitState
    {
        /// <summary>
        /// Causes the active unit to move in the specified direction.
        /// </summary>
        /// <param name="unit">The unit to move.</param>
        /// <param name="d">The direction in which the unit should move.</param>
        /// <returns>True if the state changes.</returns>
        bool Move(IActive unit, Direction d);

        /// <summary>
        /// Causes the active unit to move in the specified direction.
        /// </summary>
        /// <param name="unit">The unit to move.</param>
        /// <param name="d">The direction in which the unit should move.</param>
        /// <returns>True if the state changes.</returns>
        bool Move(IActive unit, BoardCoordinate coord);

        /// <summary>
        /// Causes the unit to attack in the specific direction.
        /// </summary>
        /// <param name="unit">The unit performing the action.</param>
        /// <param name="target">The target of the ability.</param>
        /// <returns>True if the state changes.</returns>
        bool InvokeOnTarget(IActive unit, IActive target, Abilities.Ability ability);

        /// <summary>
        /// Causes the active unit to rest.
        /// </summary>
        /// <param name="unit">The unit whose state should change.</param>
        /// <returns>True if the state changes.</returns>
        bool Rest(IActive unit);

        /// <summary>
        /// Causes a unit to be refreshed.
        /// </summary>
        /// <param name="unit"></param>
        /// <returns></returns>
        bool Refresh(IActive unit);
    }
}
