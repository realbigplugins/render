'use strict';
module.exports = function (grunt) {

    var SOURCE_DIR = 'src/',
        BUILD_DIR = 'build/';

    // Load all grunt tasks
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
                files: [SOURCE_DIR + 'assets/scss/**/*.scss'],
                tasks: ['sass', 'autoprefixer']
            },
            js: {
                files: [SOURCE_DIR + 'assets/js/source/*.js'],
                tasks: ['uglify:src']
            },
            livereload: {
                files: [
                    SOURCE_DIR + '**/*.html',
                    SOURCE_DIR + '**/*.php',
                    SOURCE_DIR + 'assets/images/**/*.{png,jpg,jpeg,gif,webp,svg}',
                    SOURCE_DIR + '!**/*ajax.php'
                ]
            }
        },

        // SASS transpiling
        sass: {
            src: {
                options: {
                    outputStyle: 'compressed'
                },
                files: [{
                    expand: true,
                    flatten: true,
                    cwd: SOURCE_DIR,
                    src: ['assets/scss/**/*.scss'],
                    dest: SOURCE_DIR + 'assets/css',
                    ext: '.min.css'
                }]
            },
            src_uncompressed: {
                options: {
                    outputStyle: 'expanded'
                },
                files: [{
                    expand: true,
                    flatten: true,
                    cwd: SOURCE_DIR,
                    src: ['assets/scss/**/*.scss'],
                    dest: SOURCE_DIR + 'assets/css',
                    ext: '.css'
                }]
            }
        },

        // Minify and concatenate scripts
        uglify: {
            src: {
                options: {
                    beautify: true
                },
                files: {
                    'src/assets/js/usl.js': ['src/assets/js/source/*.js'],
                    'src/assets/js/usl-admin.js': ['src/assets/js/source/admin/*.js', '!src/assets/js/source/admin/tinymce.js']
                }
            },
            src_min: {
                files: {
                    'src/assets/js/usl.min.js': ['src/assets/js/source/*.js'],
                    'src/assets/js/usl-admin.min.js': ['src/assets/js/source/admin/*.js', '!src/assets/js/source/admin/tinymce.js']
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
                cwd: SOURCE_DIR,
                dest: SOURCE_DIR,
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
                        cwd: SOURCE_DIR,
                        src: [
                            '**',
                            '!**/.{svn,git}/**', // Ignore VCS settings
                            '!**/.{idea}/**' // Ignore .idea project settings
                        ],
                        dest: BUILD_DIR
                    }
                ]
            }
        }
    });

    // Register tasks
    grunt.registerTask('Watch', ['watch']);
    grunt.registerTask('Build', ['copy']);
};