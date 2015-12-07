module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    concat: {
      options: {
        separator: "\n"
      },
      dist: {
        src: ['js/lib/*.js','js/main.js'],
        dest: 'js/script.js'
      }
    },
    sass: {
      dist: {
        options: {
          style: 'compressed'
        },
        files: {                                    // Dictionary of files
          'css/style.css': 'css/style.scss'         // 'destination': 'source'
        }
      }
    },
    uglify: {
      options: {
        sourceMap : true,
        mangle : false
      },
      my_target: {
        files: {
          'js/script.min.js': ['js/script.js']
        }
      }
    },
    watch: {
      css: {
        files: ['css/*.scss'],
        tasks: ['sass']
      },
      scripts: {
        files: ['js/lib/*.js','js/main.js'],
        tasks: ['concat', 'uglify']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['watch']);
};