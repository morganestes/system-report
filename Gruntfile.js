module.exports = function ( grunt ) {

	'use strict';

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		addtextdomain: {
			options: {
				textdomain: 'morgan-am-system-report',
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: ['*.php', '**/*.php', '!\.git/**/*', '!bin/**/*', '!node_modules/**/*', '!tests/**/*', '!vendor/**/*.php']
			}
		},

		wp_readme_to_markdown: {
			plugin: {
				files: {
					'README.md': 'readme.txt'
				}
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: ['\.git/*', 'bin/*', 'node_modules/*', 'tests/*', 'vendor/*'],
					mainFile: 'morgan-am-system-report.php',
					potFilename: 'morgan-am-system-report.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		phpcs: {
			plugin: {
				src: ['**/*.php', '!vendor/**/*.php', '!node_modules/**/*.php']
			},
			options: {
				bin: 'vendor/bin/phpcs',
			}
		},

		babel: {
			options: {
				sourceMap: true,
				"presets": ["@wordpress/default"]
			},
			dist: {
				files: {
					'assets/js/app.js': 'src/js/app.js'
				}
			}
		},

		uglify: {
			options: {
				mangle: {
					reserved: ['jQuery', 'Backbone', '_']
				}
			},
			app: {
				files: {
					'assets/js/app.min.js': ['assets/js/app.js']
				}
			}
		}
	} );

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.loadNpmTasks( 'grunt-phpcs' );

	grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );
	grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );
	grunt.registerTask( 'lint', ['phpcs'] );
	grunt.registerTask( 'js', ['babel', 'uglify'] );

	grunt.util.linefeed = '\n';

};
