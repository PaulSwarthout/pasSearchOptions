# pasSearchOptions
  Searches the {prefix}_options (e.g., wp_options) table in a WordPress installation for partial matches.

As is often the case, while developing in WordPress, we need to see what got written to the {prefix}_options table.
Any option that is returned may be deleted. This is useful when you're testing the code that uses, creates, or destroys the option using 
the add_option(), update_option(), delete_option(), or get_option() WordPress functions. **Note:** *this plugin accesses the database table directly.*

# **ATTENTION**

## *YOU CAN TRASH YOUR WORDPRESS INSTALLATION WITH THIS PLUGIN*

You can search for anything. You can skip entering a search string and get a complete list of the {prefix}_options table. And then 
you can delete random options. This plugin will allow that.

## *THEREFORE*
- If you're not a developer, do not play with this plugin. Your website may be unusable when you're done playing.
- If you are a developer, use it wisely and carefully. Make sure that you are fully aware of what you're deleting.
- If you do not delete anything, then this plugin is perfectly safe.
