# File Frenzy

Display files from your server directories in sortable data tables.

**Note:** This plugin has no security measures in place yet so be careful to let non-technical people use this.

## Usage

The most basic use of this plugin is this:
```
[filefrenzy path="/download"]
```
No further setup required. It will display a list of files present in that folder and link to it. It will show an icon and the filesize.

### options
**path** (mandatory)
Specify the path from the document root. `path="/download"`

**order**
Specify the ordering of the list using `<columnname>-<ordering>` like `order="filename-desc"`
Valid columnnames are 'filetype', 'filename' and 'filetype' or the number of the column starting at 0 for the first column. Valid sort options are 'asc' and 'desc'.

**filesizedecimals**
Specify the number of decimals to show in the filesize column. `filesizedecimals=1`

**whitelistedextensions**
Specify allowed extensions for files. Using this will cause the plugin to only show files with matching extensions. For example `whitelistedextensions="pdf,doc"` will only display files with the pdf or doc extension. Multiple entries must be comma separated.

**blacklistedextensions**
Specify not-allowed extensions for files. Using this will cause the plugin to HIDE files with matching extensions. For example `blacklistedextensions="php"` will NOT display file with the php extension. Multiple entries must be comma separated.
*Please note that the php extension is blacklisted by default.*

**whitelistedfilenames**
Specify allowed names for files. Using this will cause the plugin to only show files with matching names. For example `whitelistedfilenames="somefile.pdf"` will only display the file called 'somefile.pdf'. Multiple entries must be comma separated.

**blacklistedfilenames**
Specify not-allowed names for files. Using this will cause the plugin to HIDE files with matching names. For example `blacklistedfilenames="index.php"` will NOT display the file called 'index.php'. Multiple entries must be comma separated.

## Todo
The whole idea of this plugin is to make the features of the old and abandoned File Away plugin available again. That is the main goal but no route has been decided. This is just the first working version.

## Contributing

Pull requests are welcome. But remember this is still very much in alpha stage so my local code might be a lot further than this current snapshot might suggest.
For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
