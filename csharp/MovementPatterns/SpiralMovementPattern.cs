using System;

namespace out_and_back.MovementPatterns
{
    /// <summary>
    /// A movement pattern that will move an entity in a spiral shape.
    /// </summary>
    class SpiralMovementPattern : ParameterizedMovementPattern
    {
        private float t;

        /// <summary>
        /// Creates a spiral movement pattern.
        /// </summary>
        /// <param name="parent">The object whose movement is defined by this pattern.</param>
        /// <param name="t">How tight the loop should be. Lower is tighter.</param>
        public SpiralMovementPattern(Entity parent, float t) : base(parent)
        {
            this.t = t;

            float x(float time) => time * 1/t *
                    (float)Math.Cos(time) * speed;
            float y(float time) => time * 1/t *
                    (float)Math.Sin(time) * speed;

            Rotate(x, y);
        }
    }
}
