module.exports = function (grunt) {
    grunt.initConfig({
        requirejs: {
            compile: {
                options: {
                    baseUrl: './js',
                    paths: {
                        'models': 'app/models',
                        'views': 'app/views',
                        'jquery': 'lib/jquery/jquery.min',
                        'jquery-ui': 'lib/jquery-ui/jquery-ui.min',
                        'backbone': 'lib/backbone/backbone-min',
                        'underscore': 'lib/underscore/underscore-min',
                        'bootstrap': 'lib/bootstrap/js/bootstrap.min',
                        'chart': 'lib/chart/chart.min',
                        'bootstrap3-typeahead': 'lib/bootstrap3-typeahead/bootstrap3-typeahead.min'
                    },
                    shim: {
                        'bootstrap': {
                            deps: ['jquery']
                        }
                    },
                    include: 'oplb-gradebook-app',
                    out: './js/oplb-gradebook-app-min.js'
                }
            }
        },
        less: {
            development: {
                options: {
                    compress: false,
                    optimization: 2
                },
                files: {
                    "GradeBook.css": "GradeBook.less"
                }
            }
        },
    });

    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.registerTask('default', ['requirejs', 'less']);
};