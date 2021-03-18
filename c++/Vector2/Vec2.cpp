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
}
