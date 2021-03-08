# Player Timer
This code comes from a personal project. It's for a companion app for a board
game I was prototyping. This code shows off using Unity's API to expose various
members to the Unity Editor, allowing me to use the editor to tweak various
values for balance. 

I defined various events to allow for the UI to update itself based on the
PlayerTimer's state in an attempt to better follow the MVC pattern. In this 
sample, the `PlayerTimer` class is the model and the `TimerDisplayController` is
the controller. The view is saved as a Unity `.prefab`, and is not included here.
