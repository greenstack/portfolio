#include <stdlib.h>
#include "Vec2.h"

namespace Origin {

	Vec2 Vec2::random(int maxx, int maxy) {
		int x = rand() % maxx;
		int y = rand() % maxy;
		return Vec2(
			(float)(x % 2 == 0 ? -x : x), 
			(float)(y % 2 == 0 ? y : -y)
		);
	}

	Vec2 Vec2::fromDir(Direction d) {
		switch (d)
		{
		case Origin::Direction::Up:
			return Vec2(0, 1);
		case Origin::Direction::Down:
			return Vec2(0, -1);
		case Origin::Direction::Left:
			return Vec2(-1, 0);
		case Origin::Direction::Right:
			return Vec2(1, 0);
		default:
			return Vec2(0, 0);
			break;
		}
	}
}
