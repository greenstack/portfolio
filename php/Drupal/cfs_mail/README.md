# CFS Mail
During my work at [BYU Capstone](https://capstone.byu.edu/), the administration
developed a need for a mailing system that would send students emails based on
certain conditions. This Drupal module was my answer to that. The majority of this
code was developed by me; however, a coworker provided a massive amount of help 
on the _Parser_ subsystem during the initial development of the module. The code
there is about 75% his and 25% mine. Otherwise, 99% of the module code is mine.

This module uses much of Drupal's API, including it's Entity, Annotation, and
Plugin API. This use of Drupal's API allows other modules to extend CFS Mail's
functionality.

Even though this module was built for BYU Capstone, it was designed in such a
way that it could be used by any other site. This also helped the site be much
more stable and modular, as if we decided we didn't need the functionality in
this module, we could uninstall it from the site and the site wouldn't crash.

The tests in the `src/tests` directory use PHPUnit.

I developed this code from Summer 2016 to Summer 2020.

Thanks to the BYU Capstone administration for letting me share this code here.
