using DualResonance.Abilities;

namespace DualResonance.Deployables.Units.States
{
    /// <summary>
    /// Represents a unit that has taken its max actions.
    /// </summary>
    class UnitRestingState : UnitState
    {
        public override bool InvokeOnTarget(IActive unit, IActive target, Ability ability)
        {
            return false;
        }

        public override bool Rest(IActive unit)
        {
            return false;
        }
    }
}
