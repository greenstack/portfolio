namespace DualResonance.Deployables.Units.States
{
    /// <summary>
    /// Represents the unit's state before it has done anything or taken action.
    /// </summary>
    class SpryUnitState : UnitState
    {
        /// <inheritdoc/>
        public override bool Move(IActive unit, Direction d)
        {
            if (unit is AbstractMoveableUnit)
            {
                (unit as AbstractMoveableUnit).Location += BoardCoordinate.FromDirection(d);
                unit.UnitState = new UnitMovedState();
                return true;
            }
            return false;
        }

        /// <inheritdoc/>
        public override bool Move(IActive unit, BoardCoordinate location)
        {
            if (unit is AbstractMoveableUnit)
            {
                (unit as AbstractMoveableUnit).Location = location;
                unit.UnitState = new UnitMovedState();
                return true;
            }
            return false;
        }

        /// <inheritdoc/>
        public override bool Refresh(IActive unit)
        {
            return false;
        }
    }
}
