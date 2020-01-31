namespace DualResonance.Deployables.Units.States
{
    public abstract class UnitState : IUnitState
    {
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

        public virtual bool Move(IActive unit, BoardCoordinate coord)
        {
            return false;
        }

        /// <summary>
        /// Causes a unit to move, if possible.
        /// </summary>
        /// <param name="unit">The unit that should move.</param>
        /// <param name="d">The direction in which the unit should move.</param>
        /// <returns>True if the state changed. Otherwise, false.</returns>
        public virtual bool Move(IActive unit, Direction d)
        {
            return false;
        }

        public virtual bool Rest(IActive unit)
        {
            unit.UnitState = new UnitRestingState();
            return true;
        }

        public virtual bool Refresh(IActive unit)
        {
            unit.UnitState = new SpryUnitState();
            return true;
        }
    }
}
