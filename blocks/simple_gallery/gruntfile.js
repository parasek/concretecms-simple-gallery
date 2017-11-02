module.exports = function(grunt) {

    require('jit-grunt')(grunt);

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        sass: {
            dist: {
                options: {
                    sourceMap: 'auto',
                    style: 'compressed'
                },
                files: {
                    'packages/simple_gallery/blocks/simple_gallery/css_files/simple-gallery.css': 'packages/simple_gallery/blocks/simple_gallery/scss/simple-gallery.scss'
                }
            }
        },

        autoprefixer: {
            options: {
                browsers: ['last 3 versions', 'ie 9', 'ie 10', 'Android 4'],
                map: true
            },
            dist: {
                src: 'packages/simple_gallery/blocks/simple_gallery/css_files/simple-gallery.css'
            }
        },

        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1,
                sourceMap: true
            },
            target: {
                files: {
                    'packages/simple_gallery/blocks/simple_gallery/css_files/simple-gallery.css': ['packages/simple_gallery/blocks/simple_gallery/css_files/simple-gallery.css']
                }
            }
        },

        watch: {
            css: {
                files: ['packages/simple_gallery/blocks/simple_gallery/scss/**/*.scss'],
                tasks: ['sass', 'autoprefixer', 'cssmin'],
                options: {
                    spawn: false
                }
            }
        }

    });

    // css tasks
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('prod', ['sass', 'autoprefixer']);

};
