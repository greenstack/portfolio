namespace DualResonance.Deployables.Units.States
{
    /// <summary>
    /// Provides the default behavior of unit states and their transitions.
    /// </summary>
    public abstract class UnitState : IUnitState
    {
        /// <inheritdoc/>
        public virtual bool InvokeOnTarget(IActive unit, IActive target, Abilities.Ability ability)
        {
            if (ability.VerifyValidTarget(unit, target))
            {
                ability.UseAbility(unit, target);
                unit.UnitState = new UnitRestingState();
                return true;
            }
            return false;
        }

        /// <inheritdoc/>
        public virtual bool Move(IActive unit, BoardCoordinate coord)
        {
            return false;
        }

        /// <inheritdoc/>
        public virtual bool Move(IActive unit, Direction d)
        {
            return false;
        }

        /// <inheritdoc/>
        public virtual bool Rest(IActive unit)
        {
            unit.UnitState = new UnitRestingState();
            return true;
        }

        /// <inheritdoc/>
        public virtual bool Refresh(IActive unit)
        {
            unit.UnitState = new SpryUnitState();
            return true;
        }
    }
}
