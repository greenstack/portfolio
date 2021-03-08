# Survival System
During my last year of grad school and as a part of my thesis game project
([Ahri and Bear](https://ahriandbear.com)), I developed a survival system. The
survival system was comprised of three subsystems: stats, the survival component,
and stat modifiers.

## Survival Stats (`FABSurvivalStat`)
I chose to make the survival stats a struct because Unreal Engine serializes 
structs directly in an actor, but it will only serialize a reference to a class.
I wanted the stats to be bundled together and easily serializable, making them
easy for game designers to edit in the engine's editor. As such, structs were the
perfect use here.

Stats have the following three main variables:
 - `MaxValue`: The maximum value for the stat.
 - `CurrentValue`: The stat's current value.
 - `RateOfChange`: The rate at which the stat's current value changes, in units/second.

Stats also have a "starting value" member. If this value were lower than 
`MaxValue` but still greater than `0`, the stat's `CurrentValue` would be set to 
the starting value when the game began. If it were higher than `MaxValue` or less 
than `0`, then the `CurrentValue` would be set to `MaxValue` at the start. This would allow designers to tweak the `MaxValue` at will, or try designing a level where the characters weren't in optimal condition.

Each stat also had a delegate that would inform listeners when its value had reached zero or when it was no longer zero. This was used by the Survival Component to listen in for when both stats were zeroed out.

Because UE4 doesn't currently support `UFUNCTION` definitions in structs, I also 
used this file to define a class that provides the various methods required to 
interact with the stats.

You'll notice that there is my comment for the `CurrentValue` in the 
`FABSurvivalStat` that says "Oh to let blueprints call streuct functions so I
can have protected/private variables...)." In hindsight, I should have made those
values `private` and declared the `UABSurvivalStatFunctions` as a friend class to the `FABSurvivalStat` struct.

## Survival Component (`AABSurvivalComponent`)
The survival component extended Unreals `UActorComponent` class. I decided this 
was appropriate because actors would require the survival component, and the actor
can listen to the events that the survival component defines.

The survival component defines two core stats - thirst and hunger. In the file,
there are also two delegates defined:
 - `FStatModifiersChanged`: fired whenever the stat modifier array in the survival component is changed. The component has two of these delegate instances: one for insertion, and one for removal. If a modifier was added, then `StatModifierAdded` is invoked; otherwise, `StatModifierRemoved` is invoked.
 - `FAnimalCriticalConditionChanged`: fired whenever both thirst and hunger reach `0`, or when one of them rises above `0` when they were both `0`. This event was used by the game to track how many of the player's characters were in critical condition, and if both of them were, it would trigger a game over.

Each frame, the component would update the thirst and the hunger stats that it contained.

## Stat Modifiers (`ABStatModifierInterface`)
We wanted the stats' `RateOfChange` to be something that could be altered from
time to time, so I developed the `ABStatModifierInterface`. The survival 
component contains an array of these; when a modifier is added to the component, 
it sorts the modifiers by the modifier's priority, then uses the pipeline pattern
to determine a stat's current `RateOfChange`. This process happens when a stat modifier is removed as well.

## Addendum
During development, the team working on _Ahri and Bear_ decided that the
survival aspect of the game was no longer needed, as it didn't support the
design pillars or themes that we'd settled on. After this, I focused on
developing the game's accessibility features.

This code was developed from September 2020 to January 2020.
