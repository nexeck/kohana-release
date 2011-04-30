Release-packages generator
==========================

The **Release Package Generator** module generates zip-archived packages, that contains accepted files from `DOCROOT` directory of your project.

Using
-----

1. Enable **Release Package Generator** module in yor bootstrap

		Kohana::modules(array(
					...
			'release' => MODPATH.'release',
					...
			));

2. Download source code and copy it into `MODPATHrelease/` or install **Release Package Generator** from git

		# git submodule add https://github.com/Leemo/kohana-release.git modules/release
		# cd modules/release && git submodule update --init


3. Follow to [release package generation dialog](../../release)

Configuration
-------------

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