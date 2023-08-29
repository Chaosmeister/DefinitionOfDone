# DefinitionOfDone

This plugin adds a simple checklist to the taskview, similar to subtasks.  
Each entry consists of a title-, a description- and a toggleable "done"-field.

It also supports a separator to visually structure the list.  
(in the description use `=` for a bigger separator / `==` for a smaller separator)  
The separator is collapsible as well.

supports [MarkdownPlus](https://github.com/creecros/MarkdownPlus)  
![image](https://github.com/Chaosmeister/DefinitionOfDone/assets/13346344/625da643-997c-416e-8e79-bdf2b6ce5cd2)

Use `-` in the descriptionfield to hide it if you don't need it.

The whole list can be exported to json and get imported from it. Imports overwrite existing lists.

The plugin checks for templates in a separate directory `plugins\DefinitionOfDone\DodTemplates\`  
Exported dods in that directory will be selectable in the task-creation and task-overview. 

To delete an entry, you have to select it first by activating the checkbox in the options column.  
This two-step inline approach prevents accidental deletion while keeping overhead to delete multiple entries on a minimum.
