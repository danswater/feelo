module.exports = function(grunt) {

	grunt.initConfig( {
		// package json
		pkg: grunt.file.readJSON('package.json'),

		// all directtories
		dirs: {
		    controllers : 'app/controllers',
		    components : 'app/controllers/components',
		    directives : 'app/directives',
		    services : 'app/services',
		    filters : 'app/filters',
		   	utils : 'app/utils', 
		   	concat : 'app/concat',
		   	build : 'app/build',
		},

		// javascript concatation 
		concat: { 

			/**********************************************
			 				APPLICATION SCRIPTS 			 
			 *********************************************/
		    // concat all 
			appjs : {
				src : [
					'<%= dirs.utils %>/*.js',
					'<%= dirs.filters %>/*.js',
					'<%= dirs.services %>/*.js',
					'<%= dirs.directives %>/*.js',
					'<%= dirs.directives %>/**/*.js',
					'<%= dirs.components %>/*.js',
					'<%= dirs.controllers %>/*.js',
				],
				dest : '<%= dirs.concat %>/app.js',	
			}		    

		}, 

		// uglifier
		uglify: {
			options: {
	            compress: true,
	            mangle: true,
	            sourceMap: true,
	            sourceMapIncludeSources: true,
	        },
			target: {
		      	files: {
		        	'<%= dirs.build %>/yamba.min.js': [ '<%= dirs.concat %>/app.js' ]
		      	}
		    }
		},

		// javascript watcher
		watch : {
			js : {
				files : [ 
					'<%= dirs.controllers %>/*.js', 
					'<%= dirs.components %>/*.js', 
					'<%= dirs.directives %>/*.js', 
					'<%= dirs.directives %>/**/*.js', 
					'<%= dirs.services %>/*.js', 
					'<%= dirs.utils %>/*.js', 
				],
				tasks : [ "concat:appjs", "uglify" ]
			}
		}


	} );

	/** grunt plugin **/
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-browser-sync');

	grunt.registerTask( "default", [ "concat", "uglify", "watch" ] );

};