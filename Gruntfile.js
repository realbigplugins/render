'use strict';
module.exports = function (grunt) {

    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({

        // Define the package
        pkg: grunt.file.readJSON('package.json'),

        // Watch for changes
        watch: {
            options: {
                livereload: true
            },
            sass: {
                files: ['src/assets/scss/**/*.scss', '!src/assets/scss/admin/**/*.scss'],
                tasks: ['sass:src', 'autoprefixer', 'notify:sass']
            },
            sass_admin: {
                files: ['src/assets/scss/admin/**/*.scss'],
                tasks: ['sass:admin', 'autoprefixer', 'notify:sass_admin']
            },
            js: {
                files: ['src/assets/js/source/*.js'],
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

        // Minify and concatenate scripts
        uglify: {
            options: {
                sourceMap: true
            },
            src: {
                files: {
                    'src/assets/js/ultimate-shortcodes-library.min.js': ['src/assets/js/source/*.js']
                }
            },
            admin: {
                files: {
                    'src/assets/js/ultimate-shortcodes-library-admin.min.js': ['src/assets/js/source/admin/*.js', '!src/assets/js/source/admin/tinymce.js']
                }
            },
            tinymce: {
                files: {
                    'src/assets/js/includes/tinymce-plugins/usl/plugin.min.js': ['src/assets/js/source/admin/tinymce.js']
                }
            }
        },

        // Transpile SASS to CSS
        sass: {
            options: {
                style: 'compressed'
            },
            src: {
                files: {
                    'src/assets/css/ultimate-shortcodes-library.min.css': 'src/assets/scss/main.scss'
                }
            },
            admin: {
                files: {
                    'src/assets/css/ultimate-shortcodes-library-admin.min.css': 'src/assets/scss/admin/admin.scss'
                }
            }
        },

        // Prefix the minified CSS
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

        // Copy files from the src working directory to the build directory, with some file processing
        copy: {
            src: {
                options: {
                    process: function( content, src ) {

                        // Remove all TODO items
                        content = content.replace(/(\n|\s)?(.*\/\/.*)(TODO|MAYBETODO|FIXME|NEXTUPDATE|MAYBEFIX|FIXED|FUTUREBUILD|REMOVE)(.*)(\n|\s)?/g, '' );
                        return content;
                    }
                },
                files: [
                    {
                        dot: true,
                        expand: true,
                        cwd: 'src/',
                        src: [
                            '**',
                            '!**/.{svn,git}/**', // Ignore VCS settings
                            '!**/.{idea}/**', // Ignore .idea project settings
                            '!**/*.map' // No maps
                        ],
                        dest: 'build/'
                    }
                ]
            }
        },

        notify: {
            sass: {
                options: {
                    title: '<%= pkg.name %>',
                    message: 'SASS Completed'
                }
            },
            sass_admin: {
                options: {
                    title: '<%= pkg.name %>',
                    message: 'SASS Admin Completed'
                }
            },
            js: {
                options: {
                    title: '<%= pkg.name %>',
                    message: 'JS Completed'
                }
            },
            js_admin: {
                options: {
                    title: '<%= pkg.name %>',
                    message: 'JS Admin Completed'
                }
            },
            js_tinymce: {
                options: {
                    title: '<%= pkg.name %>',
                    message: 'JS tinymce Completed'
                }
            }
        }
    });

    // Register tasks
    grunt.registerTask('Watch', ['watch']);
    grunt.registerTask('Build', ['copy']);
};