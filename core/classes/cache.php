<?php

/**
 * Cache Class
 *
 * This caching class is used around the website to reduce database
 * hits and resource-intensive operations. It stores objects in groups
 * and allows for quick and simple access to them.
**/

class Cache {

	// Array to hold all cached objects
	private $cache = [];

	// Array to hold all group names
	private $groups = [];

	// Counters for statistic purposes
	public $hits = 0;
	public $misses = 0;

	/**
	 * Adds an object to the cache (if it doesn't already exist)
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $key The identifier for this cached entry
	 * @param mixed $value The object to cache
	 * @param string $group The group to store the object under
	**/
	public function add( $key, $value, $group='default' ) {

		// Default group.
		if ( empty($group) )
			$group = 'default';

		// Check if cache entry already exists.
		if ( $this->exists($key, $group) )
			return false;

		return $this->set($key, $value, $group);

	}

	/**
	 * Sets the contents of a cached object
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $key The identifier for this cached entry
	 * @param mixed $value The object to cache
	 * @param string $group The group to store the object under
	**/
	public function set( $key, $value, $group='default' ) {

		// Default group.
		if ( empty($group) )
			$group = 'default';

		// If value is an object, clone it for a unique reference.
		if ( is_object($value) )
			$value = clone $value;

		$this->cache[$group][$key] = $value;
		return true;

	}

	/**
	 * Gets a given cache object in a given group
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $key The identifier for the cached entry
	 * @param string $group The group of the cached entry
	**/
	public function get( $key, $group='default' ) {

		// Default group.
		if ( empty($group) )
			$group = 'default';

		// Search for cache entry.
		if ( $this->exists($key, $group) ) {
			$this->hits += 1;

			if ( is_object($this->cache[$group][$key]) )
				return clone $this->cache[$group][$key];
			else
				return $this->cache[$group][$key];

		}

		$this->misses += 1;
		return false;

	}

	/**
	 * Returns boolean whether or not cache object exists
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $key The identifier for the cached entry
	 * @param string $group The group of the cached entry
	**/
	public function exists( $key, $group ) {
		return isset( $this->cache[$group] ) && isset( $this->cache[$group][$key] );
	}

}

/**
 * Global function to add objects to the cache
 *
 * @since 3.0.0
 *
 * @param int|string $key The identifier for the cached entry
 * @param object $value Object or value to add to the cached entry
 * @param string $group The group of the cached entry
 * @return false|object False on failiure, cached object on success
**/
function cache_add( $key, $value, $group='' ) {
	global $cache;
	return $cache->add($key, $value, $group);
}

/**
 * Global function to get objects from the cache
 *
 * @since 3.0.0
 *
 * @param int|string $key The identifier for the cached entry
 * @param string $group The group for the cached entry
 * @return false|object False if doesn't exist, cached object on success
**/
function cache_get( $key, $group='' ) {
	global $cache;
	return $cache->get($key, $group);
}