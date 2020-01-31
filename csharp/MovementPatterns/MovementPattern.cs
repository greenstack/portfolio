using Microsoft.Xna.Framework;
using System;

namespace out_and_back.MovementPatterns
{
    /// <summary>
    /// Helps determine the movement of projectiles and enemies.
    /// </summary>
    internal abstract class MovementPattern
    {
        protected float speed;
        protected bool paused = false;
        protected bool paused_nonPlyr = false;
        private Entity parentEntity;

        public MovementPattern(Entity parent)
        {
            speed = parent.Speed;
            parentEntity = parent;
        }

        public abstract Vector2 getPosition();
        public abstract void Update(int deltaTime);

        protected void checkForPaused()
        {
            paused = parentEntity.currentGame.paused;
            paused_nonPlyr = parentEntity.currentGame.paused_nonPlyr;
        }

        /// <summary>
        /// Creates a limacon movement pattern.
        /// </summary>
        /// <param name="parent">The entity being moved.</param>
        /// <param name="a">Affects the shape of the loop.</param>
        /// <param name="b">Affects how far out the loop reaches.</param>
        /// <returns>A Limacon movement pattern.</returns>
        public static MovementPattern Limacon(Entity parent, int a, int b)
        {
            return new LimaconMovementPattern(parent, a, b);
        }

        /// <summary>
        /// Creates a spiral movement pattern.
        /// </summary>
        /// <param name="parent">The entity owning this movement pattern.</param>
        /// <param name="game">The game this pattern is working in.</param>
        /// <returns>A Spiral Movement Pattern.</returns>
        public static MovementPattern Spiral(Entity parent)
        {
            return new SpiralMovementPattern(parent, 1);
        }

        /// <summary>
        /// While you could just set an entity's velocity to zero, this works just
        /// as well, if not better, since there's no math being performed when finding
        /// the location of the unit based on time.
        /// </summary>
        /// <param name="parent"></param>
        /// <returns></returns>
        public static MovementPattern Stationary(Entity parent)
        {
            return new StationaryMovementPattern(parent);
        }

        /// <summary>
        /// Creates a movement pattern that moves the object in a straight line.
        /// </summary>
        /// <param name="parent">The entity that is moving.</param>
        /// <param name="limit">How far this movement pattern should take the unit before ending.</param>
        /// <returns>A movement pattern that will move a projectile in a single direction.</returns>
        public static MovementPattern Straight(Entity parent, float limit)
        {
            return new StraightMovementPattern(parent, limit);
        }

        public static MovementPattern Straight(Entity parent)
        {
            return Straight(parent, float.PositiveInfinity);
        }

        /// <summary>
        /// Creates a Yoyo Movement Pattern that causes an object to move back and forth.
        /// </summary>
        /// <param name="parent">The entity that is moving.</param>
        /// <param name="cycles">The amount of times the back and forth should occur. A non-positive number represents infinity.</param>
        /// <returns>The YoyoMovementPattern.</returns>
        public static MovementPattern Yoyo(Entity parent, int cycles)
        {
            return new YoyoMovementPattern(parent, cycles);
        }

        public static MovementPattern Yoyo(Entity parent)
        {
            return Yoyo(parent, 1);
        }

        /// <summary>
        /// Creates a movement pattern that 
        /// </summary>
        /// <param name="parent">The entity that is using this movement pattern.</param>
        /// <param name="game">The game the entity exists in.</param>
        /// <returns></returns>
        public static MovementPattern YoyoFollow(Entity parent)
        {
            return new YoyoMovementPatternFollow(parent);
        }

        public static MovementPattern YoyoFollow(Entity parent, float maxDistance)
        {
            return new YoyoMovementPatternFollow(parent, maxDistance);
        }

        public static MovementPattern PursueEntity(Entity parent, Entity target)
        {
            return new PursueEntityPattern(parent, target);
        }

        /// <summary>
        /// Causes the projectile to become paused.
        /// </summary>
        protected void ToggleTimePause()
        {
            paused = !paused;
        }

        public event EventHandler MovementCompleted;

        protected virtual void CompleteMovement(EventArgs e)
        {
            EventHandler handle = MovementCompleted;
            MovementCompleted?.Invoke(this, e);
        }
    }
}
