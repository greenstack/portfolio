using Microsoft.Xna.Framework;

namespace out_and_back.MovementPatterns
{
    /// <summary>
    /// A pattern for entities that don't move at all.
    /// </summary>
    class StationaryMovementPattern : DeltaMovementPattern
    {
        public StationaryMovementPattern(Entity parent) : base(parent)
        {
        }
        protected override Vector2 ComputeDelta(int deltaTime)
        {
            return Vector2.Zero;
        }
    }
}
