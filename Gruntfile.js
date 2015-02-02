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
                files: ['src/assets/scss/admin/**/*.scss', 'src/assets/scss/_ick13467' +
                'global.scss'],
                tasks: ['sass:admin', 'autoprefixer', 'notify:sass_admin']
            },
            js: {
                files: ['src/assets/js/source/**/*.js', '!src/assets/js/source/admin/**/*/js'],
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

        // Transpile SASS to CSS
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

                        var version = grunt.config.get('pkg.version'),
                            name = grunt.config.get('pkg.name'),
                            description = grunt.config.get('pkg.description'),
                            author = grunt.config.get('pkg.author'),
                            author_uri = grunt.config.get('pkg.author_uri'),
                            plugin_uri = grunt.config.get('pkg.plugin_uri');

                        // Add plugin header
                        if (src == 'src/render.php') {
                            var header = '/*\n' +
                                ' * Plugin Name: ' + name + '\n' +
                                ' * Description: ' + description + '\n' +
                                ' * Version: ' + version + '\n' +
                                ' * Author: ' + author + '\n' +
                                ' * Author URI: ' + author_uri + '\n' +
                                ' * Plugin URI: ' + plugin_uri + '\n' +
                                ' * Text Domain: Render\n' +
                                ' * Domain Path: /languages/\n' +
                                ' */';
                            content = '<?php\n' + header + content.slice(5);
                        }
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
                            '!assets/images/**', // Don't transfer images, they don't copy right
                            '!assets/icons/**', // Don't transfer icons, they don't copy right
                            '!**/.{svn,git}/**', // Ignore VCS settings
                            '!**/.{idea}/**' // Ignore .idea project settings
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
            },
            build: {
                options: {
                    title: '<%= pkg.name %>',
                    message: 'NOTE: Manually copy icons and images.'
                }
            }
        }
    });

    // Register tasks
    grunt.registerTask('Watch', ['watch']);
    grunt.registerTask('Build', ['copy', 'notify:build']);
};