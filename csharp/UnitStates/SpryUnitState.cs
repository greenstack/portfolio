namespace DualResonance.Deployables.Units.States
{
    class SpryUnitState : UnitState
    {
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

        public override bool Refresh(IActive unit)
        {
            return false;
        }
    }
}
