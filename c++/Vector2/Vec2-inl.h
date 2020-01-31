#include "Vec2.h"
#pragma once

namespace Origin {

	inline Vec2 Vec2::random() {
		return Vec2((float)rand(), (float)rand());
	}

	inline Vec2 Vec2::random(int max) {
		return Vec2::random(max, max);
	}

	inline Vec2 operator+(const Vec2& lhs, const Vec2& rhs)
	{
		return Vec2(lhs.X() + rhs.X(), lhs.Y() + rhs.Y());
	}

	inline float Vec2::norm() const {
		return static_cast<float>(sqrt(x * x + y * y));
	}

	inline Vec2 Vec2::normal() const {
		return *this / norm();
	}

	inline void Vec2::normalize() {
		*this = normal();
	}

	inline Vec2& Vec2::operator+=(const Vec2 & r)
	{
		x += r.x;
		y += r.y;
		return *this;
	}

	inline Vec2 operator-(const Vec2& rhs) {
		return Vec2(-rhs.X(), -rhs.Y());
	}

	inline Vec2 operator-(const Vec2& lhs, const Vec2& rhs)
	{
		return Vec2(lhs.X() - rhs.X(), lhs.Y() - rhs.Y());
	}

	inline Vec2& Vec2::operator-=(const Vec2& r) {
		x -= r.x;
		y -= r.y;
		return *this;
	}

	inline Vec2 operator*(const Vec2& lhs, const Vec2& rhs)
	{
		return Vec2(lhs.X() * rhs.X(), lhs.Y() * rhs.Y());
	}

	inline Vec2& Vec2::operator*=(const Vec2 & r)
	{
		x *= r.x;
		y *= r.y;
		return *this;
	}

	inline Vec2 operator*(const Vec2& lhs, float i)
	{
		return Vec2(lhs.X() * i, lhs.Y() * i);
	}

	inline Vec2& Vec2::operator*=(float i)
	{
		x *= i;
		y *= i;
		return *this;
	}

	inline Vec2 operator*(float i, const Vec2& r) {
		return r * i;
	}

	inline Vec2 operator/(const Vec2& lhs, const Vec2& rhs)
	{
		return Vec2(lhs.X() / rhs.X(), lhs.Y() / rhs.Y());
	}

	inline Vec2& Vec2::operator/=(const Vec2 & r)
	{
		x /= r.x;
		y /= r.y;
		return *this;
	}

	inline Vec2 operator/(const Vec2& lhs, float i)
	{
		return Vec2(lhs.X() / i, lhs.Y() / i);
	}

	inline Vec2& Vec2::operator/=(float i)
	{
		x /= i;
		y /= i;
		return *this;
	}

	inline Vec2::operator GLib::Point2D() const
	{
		auto pt = GLib::Point2D();
		pt.x = x;
		pt.y = y;
		return pt;
	}

	inline bool operator==(const Vec2& lhs, const Vec2& rhs)
	{
		return lhs.X() == rhs.X() && lhs.Y() == rhs.Y();
	}

	inline bool operator!=(const Vec2& lhs, const Vec2& rhs)
	{
		return lhs.X() != rhs.X() || lhs.Y() != rhs.Y();
	}
}
