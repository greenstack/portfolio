# Vector2
I created this Vector2 class for my C++ for Game Developers class, which I took
during my first semester of grad school. In this class, I strive to make my
`Vec2` class have the behaviors of a built-in C++ type by overloading all the
appropriate operators. Because the operations done on a Vec2 are so small, most
of the functions are inlined, and their functionality is found in `Vec2-inl.h`.
Getters are also all const-correct. Some external library code was also
provided to us later on which contained a `Point2D` class (line 71 of `Vec2.h`)
; I wanted to be able to quickly cast from my `Vec2` class to the `Point2D`, so
I implemented a cast operator for that as well.

This code began development in September 2019 and evolved over time.
