/**
 * This file contains some unit tests that I wrote for my smart and weak
 * pointer classes. I've made sure to comment thoroughly what each assertion or
 * group of assertions is trying to do.
 */

// WeakPtr includes a reference to SmartPtr.h
#include <assert.h>
#include "WeakPtr.h"
#include "Vec2.h"

void ptrTests() {
	using namespace Origin::Core;

	// This won't compile: smart pointer requires an rvalue pointer, to
	// enforce that we won't accidentally delete the pointer when something
	// else is looking for it.
	// int* wontCompile = new int(5);
	// auto wontCompileSP = SmartPtr<int>(wontCompile);
	
	auto intSmartPtr = SmartPtr<int>(new int(5));
	// make sure that the created pointer is valid
	assert(intSmartPtr);
	{ 
		/* Let's do this in another block to ensure that anotherSmartPtr is 
		destroyed. Afterwards, we want to ensure that intSmartPtr is still valid.*/
		auto anotherSmartPtr = SmartPtr<int>(intSmartPtr);
		assert(anotherSmartPtr);

		// Let's test getting this guy
		assert(*intSmartPtr == 5);
		// Ensure standard ops work on him
		assert(*anotherSmartPtr + *intSmartPtr == *intSmartPtr * 2);
		*intSmartPtr = 6;
		// Make sure assignment works correctly
		assert(*intSmartPtr == 6);
	}
	// Ensure intSmartPtr is still valid
	assert(intSmartPtr);

	// Let's try out assigning nullptr to our smart ptr
	intSmartPtr = nullptr;
	// Make sure it's _not_ valid
	assert(!intSmartPtr);
	// Let's see if it is nullptr...
	assert(intSmartPtr == nullptr);

	using namespace Origin;

	const int vec2XStart = 4;
	const int vec2YStart = 2;
	
	auto outerSmartPtr = SmartPtr<Vec2>(new Vec2(vec2XStart, vec2YStart));
	auto outerWeakPtr = WeakPtr<Vec2>(outerSmartPtr);
	{
		auto sp_vec2 = SmartPtr<Vec2>(new Vec2(vec2XStart, vec2YStart));
		auto nm_vec2 = Vec2(vec2XStart, vec2YStart);
		assert(nm_vec2 == *sp_vec2);
		*sp_vec2 *= Vec2(2);
		const int vec2XDouble = vec2XStart * 2;
		const int vec2YDouble = vec2YStart * 2;

		// Check natural method calls
		assert(sp_vec2->X() == vec2XDouble);
		sp_vec2->Y(5);
		assert((*sp_vec2).Y() == 5);

		outerSmartPtr = sp_vec2;
		assert(!outerWeakPtr);
		{
			WeakPtr<Vec2> weak = WeakPtr<Vec2>(sp_vec2);
			assert(weak);
			assert(weak.weakUses() == 1);

			outerWeakPtr = weak;
			assert(outerWeakPtr);

			// Let's test out promotions
			SmartPtr<Vec2> promoted = WeakPtr<Vec2>::promote(weak);
			assert(promoted->X() == vec2XDouble);
			promoted->Y(vec2YStart);
			// Make sure data changes are the same across pointers
			assert(promoted->Y() == vec2YStart);
			assert(sp_vec2->Y() == vec2YStart);

			// Let's make sure it happens in the other direction - can't
			// be too careful
			sp_vec2->X(vec2XStart);
			assert(promoted->X() == vec2XStart);
			assert(sp_vec2->X() == vec2XStart);

			// Let's make sure that this works
			*promoted += *sp_vec2;
			assert(promoted->X() == vec2XDouble);
			assert(sp_vec2->X() == vec2XDouble);
			assert(promoted->Y() == vec2YDouble);
			assert(sp_vec2->Y() == vec2YDouble);

			// Let's be sure we've got the right count of smart uses.
			assert(promoted.smartUses() == 3);
			// Let's reassign the outerSmartPointer to make sure references are tracked right
			outerSmartPtr = SmartPtr<Vec2>(new Vec2(vec2XStart, vec2YStart));
			assert(promoted.smartUses() == 2);
		}
	}
	// With outerSmartPtr assigned to another object and the other objects
	// destroyed, the weak pointer shouldn't point to anything.
	assert(!outerWeakPtr);
	outerSmartPtr = nullptr;
}