
module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        copy: {
            materialize_css: {
                expand: true,
                cwd: 'node_modules/materialize-css/dist/css',
                src: ['**/*'],
                dest: '../public/css'
            },
            materialize_js: {
                expand: true,
                cwd: 'node_modules/materialize-css/dist/js',
                src: ['**/*'],
                dest: '../public/js'
            },
            materialize_fonts: {
                expand: true,
                cwd: 'node_modules/materialize-css/dist/fonts',
                src: ['**/*'],
                dest: '../public/fonts'
            },
            jquery: {
                expand: true,
                cwd: 'node_modules/jquery/dist/',
                src: ['jquery.js', 'jquery.min.js'],
                dest: '../public/js'
            },
            fontAwesome_css: {
                expand: true,
                cwd: 'node_modules/font-awesome/css',
                src: ['font-awesome.min.css'],
                dest: '../public/css'
            },
            fontAwesome_fonts: {
                expand: true,
                cwd: 'node_modules/font-awesome/fonts',
                src: ['FontAwesome.otf','fontawesome-webfont.eot', "fontawesome-webfont.svg", "fontawesome-webfont.ttf",
                    "fontawesome-webfont.woff", "fontawesome-webfont.woff2" ],
                dest: '../public/fonts'
            }
        },

        less: {
            styles: {
                options: {
                    paths: ['../less'],
                    sourceMap: true
                },
                files: {
                    '../public/css/styles.css': '../less/styles.less'
                }
            },
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-exec');
    grunt.loadNpmTasks('grunt-contrib-less');
};