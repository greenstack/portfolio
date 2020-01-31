#pragma once
#include "../DataStruct.h"
#include <GLib/GLib.h>

#include <math.h>

namespace Origin {
	class Vec2;

	class Vec2
	{
	public:
		// Creates a vec2 with x and y initialized to 0.
		Vec2() : x(0), y(0) {};
		// Creates a vec2 with x and y initialized to {value}.
		explicit Vec2(float value) : x(value), y(value) {};
		// Creates a vec2 with the specified x and y value.
		Vec2(float x, float y) : x(x), y(y) {};
		~Vec2() {};

		static Vec2 random();
		// Creates a vec2 at a random position.
		// @param max: the maximum x or y value.
		// @param abs: whether or not the value must be positive.
		static Vec2 random(int max);
		// Creates a vec2 at a random position.
		// @param maxx: the maximum x value.
		// @param maxy: the maximum y value.
		// @param abs: whether or not the value must be positive.
		static Vec2 random(int maxx, int maxy);

		static Vec2 fromDir(Direction);
		
		// Returns the norm of this vector.
		float norm() const;

		// Returns the normal of this vector.
		Vec2 normal() const;

		// Normalizes this vector.
		void normalize();

		// Getters

		float X() const { return x; }
		float Y() const { return y; }

		// Setters;

		void X(float n_x) {	x = n_x; }
		void Y(float n_y) { y = n_y; }

		// Memberwise addition and assignment of a vector
		Vec2& operator+=(const Vec2& r);

		// Memberwise subtraction and assignment of a vector
		Vec2& operator-=(const Vec2& r);

		// Memberwise multiplication/assignment of two vectors
		Vec2& operator*=(const Vec2& r);
		
		// Scalar multiplication and assignment of a vector
		Vec2& operator*=(float i);

		// Memberwise division and assignment of a vector
		Vec2& operator/=(const Vec2& r);
		
		// Scalar division and assignment of a vector
		Vec2& operator/=(float i);

		operator GLib::Point2D() const;
	private:
		float x, y;
	};

	// Memberwise addition of two vectors
	Vec2 operator+(const Vec2&lhs, const Vec2& rhs);
	// Unary negation operator
	Vec2 operator-(const Vec2& rhs);
	// Memberwise subtraction of two vectors
	Vec2 operator-(const Vec2&lhs, const Vec2& rhs);
	// Memberwise multiplication of two vectors
	Vec2 operator*(const Vec2&lhs, const Vec2& rhs);
	// Scalar multiplication of a vector
	Vec2 operator*(const Vec2&lhs, float rhs);
	// Allows left-to-right associativity with Vector multiplication
	Vec2 operator*(float i, const Vec2& r);
	// Memberwise division of two vectors
	Vec2 operator/(const Vec2&lhs, const Vec2& rhs);
	// Scalar division of a vector
	Vec2 operator/(const Vec2&lhs, float rhs);
	// Memberwise equality comparison of two vectors
	bool operator==(const Vec2&lhs, const Vec2&rhs);
	// Memberwise inequality comparison of two vectors
	bool operator!=(const Vec2&lhs, const Vec2&rhs);
}

#include "Vec2-inl.h"
