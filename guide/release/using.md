# Using

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