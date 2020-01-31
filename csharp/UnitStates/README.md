# States
In this project, I was developing a tactics-style game. In it, the player would
control units on a grid in a turn-based system. Units could be moved in any
order, and I wanted a system that would allow me to quickly figure out what
units could do what and how many units the player still hadn't used. I used
these states to keep track of this, as it helped me modify the behavior of the
units on the board without having to resort to several potentially deep-nested
if statements in the code.

This solution solved two problems: the issue of tracking untapped units, what
currently selected units were able to do, as well as keeping the code clean and
potentially giving me the option to expand the capabilities of the units and
the actions available to them without too much headache.
