<?php

	/*
	 * This file is part of the Ariadne Component Library.
	 *
	 * (c) Muze <info@muze.nl>
	 *
	 * For the full copyright and license information, please view the LICENSE
	 * file that was distributed with this source code.
	 */
	 
	namespace arc\context;
	
	class ContextStack extends \SplStack implements \arc\KeyValueStoreInterface {
		protected $position = 0;
		
		public function __construct() {
			parent::push( array(
				'path' => '/'
			) );
		}
	
		public function push( $values ) {
			// merge values with last entry
			$original = $this->top();
			$values += $original;
			parent::push( $values );
		}
	
		public function peek( $level = 0 ) {
			$size = parent::count();
			$offset = $site - ( $level + 1 );
			return parent::offsetGet( $offset );
		}
	
		public function getPath() {
			$context = $this->top();
			return $context['path'];
		}
		
		public function putVar( $name, $value ) {
			$context = $this->pop();
			if ( isset( $value ) ) {
				$context[$name] = $value;
			} else {
				unset( $context[$name] );
			}
			$this->push( $context );
		}
		
		public function getVar( $name ) {
			$context = $this->top();
			return $context[$name];
		}
		
		// each stack entry in the context is an array
		// use arrayaccess on that array, not on the stack
		public function offsetExists( $offset ) {
			$context = $this->top();
			return isset( $context[$offset] );
		}
		
		public function offsetSet( $offset, $value ) {
			$this->putVar( $offset, $value );
		}
		
		public function offsetGet( $offset ) {
			return $this->getVar( $offset );
		}
		
		public function offsetUnset( $offset ) {
			$this->putVar( $offset, null );
		}
		
		// countable interface on the context array, not the stack
		public function count() {
			return count( $this->top() );
		}
		
		// iterable interface on the context array, not the stack
		public function current() {
			$context = $this->top();
			$keys = array_keys( $context );
			$key = $keys[ $this->position ];
			return isset($key) ? $context[ $key ] : null;
		}
		
		public function key() {
			$context = $this->top();
			$keys = array_keys( $context );
			$key = $keys[ $this->position ];
			return $key;
		}
		
		public function next() {
			++$this->position;
		}
		
		public function rewind() {
			$this->position = 0;
		}
		
		public function valid() {
			$context = $this->top();
			$keys = array_keys( $context );
			$key = $keys[ $this->position ];
			return isset($key);
		}
		
	}
