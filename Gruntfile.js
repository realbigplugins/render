'use strict';
module.exports = function (grunt) {

    // Define the package
    var pkg = grunt.file.readJSON('package.json'),
        image_ignore = '**/*.{png,gif,jpg,ico,psd,svt,ttf,eot,woff}';

    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({

        /**
         * Watch for changes and automatically fire tasks.
         *
         * @since 1.0.0
         */
        watch: {
            options: {
                livereload: true
            },
            sass: {
                files: ['src/assets/scss/**/*.scss', '!src/assets/scss/admin/**/*.scss', '!src/assets/scss/tinymce/**/*.scss'],
                tasks: ['sass:src', 'autoprefixer', 'notify:sass']
            },
            sass_admin: {
                files: ['src/assets/scss/admin/**/*.scss', 'src/assets/scss/_global.scss'],
                tasks: ['sass:admin', 'autoprefixer', 'notify:sass_admin']
            },
            sass_tinymce: {
                files: ['src/assets/scss/tinymce/**/*.scss', 'src/assets/scss/_global.scss'],
                tasks: ['sass:tinymce', 'autoprefixer', 'notify:sass_tinymce']
            },
            js: {
                files: ['src/assets/js/source/**/*.js', '!src/assets/js/source/admin/**/*.js'],
                tasks: ['uglify:src', 'notify:js']
            },
            js_admin: {
                files: ['src/assets/js/source/admin/*.js', '!src/assets/js/source/admin/tinymce.js'],
                tasks: ['uglify:admin', 'notify:js_admin']
            },
            js_tinymce: {
                files: ['src/assets/js/source/admin/tinymce.js'],
                tasks: ['uglify:tinymce', 'notify:js_tinymce']
            },
            livereload: {
                files: [
                    'src/**/*.html',
                    'src/**/*.php',
                    'src/assets/images/**/*.{png,jpg,jpeg,gif,webp,svg}',
                    'src/!**/*ajax.php'
                ]
            }
        },

        /**
         * Minify and concatenate javascript files.
         *
         * @since 1.0.0
         */
        uglify: {
            options: {
                sourceMap: true
            },
            src: {
                files: {
                    'src/assets/js/render.min.js': ['src/assets/js/source/**/*.js', '!src/assets/js/source/admin/**/*.js']
                }
            },
            admin: {
                files: {
                    'src/assets/js/render-admin.min.js': ['src/assets/js/source/admin/*.js', '!src/assets/js/source/admin/tinymce.js']
                }
            },
            tinymce: {
                files: {
                    'src/assets/js/includes/tinymce-plugins/render/plugin.min.js': ['src/assets/js/source/admin/tinymce.js']
                }
            }
        },

        /**
         * Transpile SASS to minified and concatenated CSS.
         *
         * @since 1.0.0
         */
        sass: {
            options: {
                style: 'compressed'
            },
            src: {
                files: {
                    'src/assets/css/render.min.css': 'src/assets/scss/main.scss'
                }
            },
            admin: {
                files: {
                    'src/assets/css/render-admin.min.css': 'src/assets/scss/admin/admin.scss'
                }
            },
            tinymce: {
                files: {
                    'src/assets/css/render-tinymce.min.css': 'src/assets/scss/tinymce/tinymce.scss'
                }
            }
        },

        /**
         * Add browser prefixes for old browser support.
         *
         * @since 1.0.0
         */
        autoprefixer: {
            options: {
                browsers: ['Android >= 2.1', 'Chrome >= 21', 'Explorer >= 7', 'Firefox >= 17', 'Opera >= 12.1', 'Safari >= 6.0']
            },
            src: {
                expand: true,
                cwd: 'src/',
                dest: 'src/',
                src: [
                    'assets/css/*.css'
                ]
            }
        },

        /**
         * Automatically update version numbers throughout and add plugin header on build.
         *
         * @since 1.0.3
         */
        'string-replace': {
            version: {
                files: {
                    'src/': ['src/**', '!' + image_ignore],
                    'init.php': ['init.php', '!' + image_ignore],
                    'README.md': ['README.md', '!' + image_ignore]
                },
                options: {
                    replacements: [{
                        // PHP doc versions
                        pattern: /\{\{VERSION}}/g,
                        replacement: pkg.version
                    }, {
                        // Version in init.php
                        pattern: /(Version: ).*/,
                        replacement: "$1" + pkg.version
                    }, {
                        // README.md version
                        pattern: /(###.*?)\d\.\d\.\d/,
                        replacement: "$1" + pkg.version
                    }, {
                        // readme.txt stable tag version
                        pattern: /(Stable tag:.*?)\d\.\d\.\d/,
                        replacement: "$1" + pkg.version
                    }, {
                        // Render constant version
                        pattern: /(define.*?RENDER_VERSION.*?)\d\.\d\.\d/,
                        replacement: "$1" + pkg.version
                    }]
                }
            },
            header: {
                files: {
                    'build/render.php': ['build/render.php', '!' + image_ignore]
                },
                options: {
                    replacements: [{
                        pattern: /\/\/\{\{HEADER}}/,
                        replacement: '/*\n' +
                        ' * Plugin Name: ' + pkg.name + '\n' +
                        ' * Description: ' + pkg.description + '\n' +
                        ' * Version: ' + pkg.version + '\n' +
                        ' * Author: ' + pkg.author + '\n' +
                        ' * Author URI: ' + pkg.author_uri + '\n' +
                        ' * Plugin URI: ' + pkg.plugin_uri + '\n' +
                        ' * Text Domain: Render\n' +
                        ' * Domain Path: /languages/\n' +
                        ' */'
                    }]
                }
            }
        },

        /**
         * Compresses images.
         *
         * @since 1.0.3
         */
        imagemin: {
            build: {
                expand: true,
                cwd: 'src/',
                src: ['**/*.{png,jpg,gif,jpeg}'],
                dest: 'src/'
            }
        },

        /**
         * Copies src files to build and syncs build directory with src directory.
         *
         * @since 1.0.3
         */
        sync: {
            options: {
                // Don't eff up images!!!
                processContentExclude: [
                    image_ignore
                ]
            },
            build: {
                updateAndDelete: true,
                files: [
                    {
                        dot: true,
                        expand: true,
                        cwd: 'src/',
                        src: [
                            '**',
                            '!**/.{svn,git}/**', // Ignore VCS settings
                            '!**/.{idea}/**', // Ignore .idea project settings
                            '!**/.DS_Store' // Ignore Mac OS dir settings
                        ],
                        dest: 'build/'
                    }
                ]
            }
        },

        /**
         * Notifies me when tasks complete.
         *
         * @since 1.0.0
         */
        notify: {
            sass: {
                options: {
                    title: pkg.name,
                    message: 'SASS Completed'
                }
            },
            sass_admin: {
                options: {
                    title: pkg.name,
                    message: 'SASS Admin Completed'
                }
            },
            sass_tinymce: {
                options: {
                    title: pkg.name,
                    message: 'SASS TinyMCE Completed'
                }
            },
            js: {
                options: {
                    title: pkg.name,
                    message: 'JS Completed'
                }
            },
            js_admin: {
                options: {
                    title: pkg.name,
                    message: 'JS Admin Completed'
                }
            },
            js_tinymce: {
                options: {
                    title: pkg.name,
                    message: 'JS tinymce Completed'
                }
            },
            build: {
                options: {
                    title: pkg.name,
                    message: 'Build for ' + pkg.version + ' complete! Be sure to add to git.'
                }
            }
        }
    });

    // Register tasks
    grunt.registerTask('Watch', ['watch']);
    grunt.registerTask('Build', ['imagemin:build', 'string-replace:version', 'sync', 'string-replace:header', 'notify:build']);
};