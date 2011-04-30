# Configuration

The release package generator has the following config options, available in `APPPATHconfig/release.php`. Each time, the formation of release, this module will automatically overwrite the configuration file.

	return array
	(
		'files'      => FILES_ARRAY,
		'extensions' => EXTENSIONS_ARRAY,
		'extensions' => NAMES_ARRAY,
	);

**FILES_ARRAY** - release package files array. IMPORTANT: files of `some_file_name.ext.rename` will be renamed to `some_file_name.ext`.

**EXTENSIONS_ARRAY** - files with that extensions will be automatically ignored.

**NAMES_ARRAY** - files and directories with that names will be automatically ignored (for example, `.git`, `.svn` etc).