## Organized Contacts ##

The plugin allows you to organize information about your companies / organizations. Plugin will add customizer settings and [contact] shortcode.

### How to use ###

```html
<!-- Print contact name -->
[contact field="name"]
<!-- Print contact image -->
[contact_image]
<!-- Print contact location -->
[contact field="city, address"]
<!-- Print contact first phone number (split by ,) -->
[contact field="phone" del="," part="1"]
<!-- Print contact second number -->
[contact field="phone" part="2"]
<!-- Print contact mail address -->
[contact field="email" before="Email: "]
<!-- Print contact work time -->
[contact field="work_time"]
<!-- Print contact socials -->
[contact field="socials"]
```