# Heap Manager
For my C++ for Game Developers class, I was required to write a heap manager.
As a part of this, I implemented guardbanding and filling to help with my own
debugging efforts, both for this project as well as for future use of this
heap manager.

Implemented in this heap manager is an allocation, free, and coallesce method.
Coallesce is used to defragment memory as best as possible, clearing out as
many adjacent and unused/empty block descriptors as possible in order to allow
larger allocations to be made.

On the subject of Block Descriptors, I decided to have the objects describing
the block of memory they pointed to be adjacent to the memory they described.
I chose to do this because it may increase the number of allocations available
to the program - a fixed number of Block Descriptors at the start or end of the
memory provided by the OS puts a hard limit on the number of allocations
available to the programmer, and also takes up space that could potentially be
used by a larger allocation.

In all parts of the heap manager, I strived to use as system-independent types
as possible. By using `uint8_t` instead of `char`, I was able to get types that
more accurately represented what I was doing with the data, as well as types
that were guaranteed across platforms that would be of a specific size. Though
this code only works on Windows, doing this would make it much simpler to port
it over to Linux or other systems.
