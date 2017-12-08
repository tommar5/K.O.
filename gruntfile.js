module.exports = function(grunt) {

  'use strict';

  require('time-grunt')(grunt);
  require('load-grunt-tasks')(grunt);
  grunt.option('force', true);

  grunt.registerTask('build', ['clean', 'concat', 'less', 'copy']);
  grunt.registerTask('default', ['build', 'watch']);

  var isRelease = false;
  grunt.cli.tasks.forEach(function (ts) {
    if (ts === 'release') {
      isRelease = true;
    }
  });

  grunt.initConfig({
    src: {
      app: {
        js: ['assets/js/app/**/*.js'],
        js_vendor: [
          'assets/vendor/jquery/dist/jquery.js',
          'assets/vendor/bootstrap/dist/js/bootstrap.js',
          'assets/vendor/datetimepicker/build/jquery.datetimepicker.full.min.js',
          'assets/vendor/select2/dist/js/select2.full.min.js',
          'assets/vendor/bootstrap-modal-form/dist/modal.min.js',
          'assets/vendor/moment/min/moment.min.js',
          'assets/vendor/multifile/jquery.MultiFile.min.js',
          'assets/vendor/fullcalendar/dist/fullcalendar.min.js',
          'assets/vendor/fullcalendar/dist/lang-all.js',
          'assets/vendor/jquery-validation/dist/jquery.validate.min.js',
          'assets/vendor/blockUI/jquery.blockUI.js'
        ],
        js_licence:['assets/js/licence.js'],
        css_vendor: [
          'assets/vendor/datetimepicker/jquery.datetimepicker.css',
          'assets/vendor/select2/dist/css/select2.min.css',
          'assets/vendor/select2-bootstrap-theme/dist/select2-bootstrap.min.css',
          'assets/vendor/fullcalendar/dist/fullcalendar.min.css'
        ],
        less: ['assets/less/app.less']
      }
    },

    clean: ['httpdocs/build/*'],

    concat: {
      app_js: {
        src: ['<%= src.app.js %>'],
        dest: 'httpdocs/build/app.js'
      },
      app_vendor_js: {
        src: '<%= src.app.js_vendor %>',
        dest: 'httpdocs/build/app-vendor.js'
      },
      app_licence_js: {
        src: '<%= src.app.js_licence %>',
        dest: 'httpdocs/build/licence.js'
      },
      app_vendor_css: {
        src: '<%= src.app.css_vendor %>',
        dest: 'httpdocs/build/app-vendor.css'
      }
    },

    copy: {
      fonts: {
        files: [
          { dest: 'httpdocs/build/fonts/', cwd: 'assets/fonts/', src: '**', expand: true},
          { dest: 'httpdocs/build/fonts/', cwd: 'assets/vendor/bootstrap/dist/fonts/', src: '**', expand: true},
          { dest: 'httpdocs/build/fonts/', cwd: 'assets/vendor/fontawesome/fonts/', src: '**', expand: true}
        ]
      },
      images: {
        files: [
          { dest: 'httpdocs/build/img/', cwd: 'assets/img/', src: '**', expand: true}
        ]
      },
      jq_map: {
        src: 'assets/vendor/jquery/dist/jquery.min.map',
        dest: 'httpdocs/build/jquery.min.map'
      }
    },

    less: {
      app: {
        options: {
          strictImports : true,
          compress: isRelease,
          sourceMap: true,
          outputSourceFiles: true,
          sourceMapURL: "app.css.map"
        },
        files: {
          'httpdocs/build/app.css': '<%= src.app.less %>'
        }
      }
    },

    uglify: {
      app: {
        src: ['<%= concat.app_js.src %>'],
        dest: '<%= concat.app_js.dest %>'
      },
      licence: {
          src: ['<%= concat.app_licence_js.src %>'],
          dest: '<%= concat.app_licence_js.dest %>'
      }
    },

    watch: {
      js_app: {
        files: ['<%= src.app.js %>'],
        tasks: ['concat:app_js']
      },
      less_app: {
        files: ['<%= src.app.less %>', 'assets/less/app/**/*.less'],
        tasks: ['less:app']
      },
      licence_app: {
        files: ['<%= src.app.js_licence %>', 'assets/less/app/**/*.less'],
        tasks: ['concat:app_licence_js']
      },
      images: {
        files: ['assets/img/**/*'],
        tasks: ['copy:images']
      }
    }

  });
};
