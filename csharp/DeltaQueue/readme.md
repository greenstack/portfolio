# Delta Queue
Written in 2023/2024 for a personal game project written in MonoGame, this is a
container that is designed to queue objects for release after a certain period
of time. This is achieved by storing the deltas between each event. Each `Update`
call will decrement the amount of time until that item is released.

A `LinkedList` was chosen as the underlying container because it allows for easy
and efficient insertion of new elements, though adding is still an O(n)
operation as the queue must search for where the new item should be placed.

The unit tests included help ensure that the `DeltaQueue` works properly. These
unit tests, which used the Nunit test framework, helped me debug the queue
quickly and efficiently, without having to wait for the game state to reach a 
point where a failure condition could be met. This allowed me to more quickly 
iterate over the `DeltaQueue` code and get it into working condition faster.
