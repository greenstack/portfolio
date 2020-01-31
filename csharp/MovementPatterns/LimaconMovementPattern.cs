using Microsoft.Xna.Framework;
using System;

namespace out_and_back.MovementPatterns
{
    /// <summary>
    /// Creates a pattern that moves objects in a Limacon shape.
    /// </summary>
    class LimaconMovementPattern : ParameterizedMovementPattern
    {
        private readonly float a;
        private readonly float b;
        private Entity parent;
        
        /// <summary>
        /// Creates a pattern that moves an object like a limacon.
        /// </summary>
        /// <param name="parent">The object moving according to this pattern.</param>
        /// <param name="a">Affects the size of the looping.</param>
        /// <param name="b">Affects how far out the looping goes.</param>
        public LimaconMovementPattern(Entity parent, float a, float b) : base(parent)
        {
            this.parent = parent;
            this.a = a;
            this.b = b;

            float x(float time) => (this.a + this.b * (float)Math.Sin(time)) * (float)Math.Cos(time) * speed;
            float y(float time) => (this.a + this.b * (float)Math.Sin(time)) * (float)Math.Sin(time) * speed;

            Rotate(x, y);
        }

        public override void Update(int deltaTime)
        {
            checkForPaused();
            if (parent.Team == Team.Player)
            {
                if (paused && !paused_nonPlyr)
                    return;
            }
            else
            {
                if (paused)
                    return;
            }
                    

            base.Update(deltaTime);
            // TODO: At 2Pi, the limacon is done.
            if (Lifetime > MathHelper.TwoPi)
                CompleteMovement(new EventArgs());
        }
    }
}
