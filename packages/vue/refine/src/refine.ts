import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import type { VisitOptions } from "@inertiajs/core";
import { useDebounceFn } from "@vueuse/core";
import type {
	Refine,
	Filter,
	Sort,
	Search,
	Direction,
	BindingOptions,
} from "./types";

export function useRefine<
	T extends object,
	K extends T[keyof T] extends Refine ? keyof T : never,
>(props: T, key: K, defaultOptions: VisitOptions = {}) {
	const refinements = computed(() => props[key] as Refine);

	/**
	 * The available filters.
	 */
	const filters = computed(
		() =>
			refinements.value.filters?.map((filter) => ({
				...filter,
				apply: (value: T, options: VisitOptions = {}) =>
					applyFilter(filter, value, options),
				clear: (options: VisitOptions = {}) => clearFilter(filter, options),
				bind: () => bindFilter(filter.name),
			})) ?? [],
	);

	/**
	 * The available sorts.
	 */
	const sorts = computed(
		() =>
			refinements.value.sorts?.map((sort) => ({
				...sort,
				apply: (options: VisitOptions = {}) =>
					applySort(sort, sort.direction, options),
				clear: (options: VisitOptions = {}) => clearSort(options),
				bind: () => bindSort(sort),
			})) ?? [],
	);

	/**
	 * The available searches.
	 */
	const searches = computed(() =>
		refinements.value.searches?.map((search) => ({
			...search,
			apply: (options: VisitOptions = {}) => applyMatch(search, options),
			clear: (options: VisitOptions = {}) => applyMatch(search, options),
			bind: () => bindMatch(search),
		})),
	);

	/**
	 * The current filters.
	 */
	const currentFilters = computed(
		() => refinements.value.filters?.filter(({ active }) => active) ?? [],
	);

	/**
	 * The current sort.
	 */
	const currentSort = computed(() =>
		refinements.value.sorts?.find(({ active }) => active),
	);

	/**
	 * The current searches.
	 */
	const currentSearches = computed(
		() => refinements.value.searches?.filter(({ active }) => active) ?? [],
	);

	/**
	 * Converts an array parameter to a comma-separated string for URL parameters.
	 */
	function delimitArray(value: any) {
		if (Array.isArray(value)) {
			return value.join(refinements.value.config.delimiter);
		}

		return value;
	}

	/**
	 * Formats a string value for search parameters.
	 */
	function stringValue(value: any) {
		if (typeof value !== "string") {
			return value;
		}

		return value.trim().replace(/\s+/g, "+");
	}

	/**
	 * Returns undefined if the value is an empty string, null, or undefined.
	 */
	function omitValue(value: any) {
		if (["", null, undefined, []].includes(value)) {
			return undefined;
		}

		return value;
	}

	/**
	 * Pipe the given value through the given transforms.
	 */
	function pipe(value: any) {
		return [delimitArray, stringValue, omitValue].reduce(
			(result, transform) => transform(result),
			value,
		);
	}

	/**
	 * Toggle the presence of a value in an array.
	 */
	function toggleValue(value: any, values: any) {
		values = Array.isArray(values) ? values : [values];

		if (values.includes(value)) {
			return values.filter((item: any) => item !== value);
		}

		return [...values, value];
	}

	/**
	 * Gets a filter by name.
	 */
	function getFilter(name: string): Filter | undefined {
		return refinements.value.filters?.find((filter) => filter.name === name);
	}

	/**
	 * Gets a sort by name.
	 */
	function getSort(
		name: string,
		direction: Direction = null,
	): Sort | undefined {
		return refinements.value.sorts?.find(
			(sort) => sort.name === name && sort.direction === direction,
		);
	}

	/**
	 * Gets a search by name.
	 */
	function getSearch(name: string): Search | undefined {
		return refinements.value.searches?.find((search) => search.name === name);
	}

	/**
	 * Whether the given filter is currently active.
	 */
	function isFiltering(name?: Filter | string): boolean {
		if (!name) {
			return !!currentFilters.value.length;
		}

		if (typeof name === "string") {
			return currentFilters.value.some((filter) => filter.name === name);
		}

		return name.active;
	}

	/**
	 * Whether the given sort is currently active.
	 */
	function isSorting(name?: Sort | string): boolean {
		if (!name) {
			return !!currentSort.value;
		}

		if (typeof name === "string") {
			return currentSort.value?.name === name;
		}

		return name.active;
	}

	/**
	 * Whether the search is currently active, or on a given match.
	 */
	function isSearching(name?: Search | string): boolean {
		if (!name) {
			return !!refinements.value.config.term;
		}

		if (typeof name === "string") {
			return currentSearches.value?.some((search) => search.name === name);
		}

		return name.active;
	}

	/**
	 * Apply the given values in bulk.
	 */
	function apply(values: Record<string, any>, options: VisitOptions = {}) {
		const data = Object.fromEntries(
			Object.entries(values).map(([key, value]) => [key, pipe(value)]),
		);

		router.reload({
			...defaultOptions,
			...options,
			data,
		});
	}

	/**
	 * Applies the given filter.
	 */
	function applyFilter(
		filter: Filter | string,
		value: any,
		options: VisitOptions = {},
	) {
		const refiner = typeof filter === "string" ? getFilter(filter) : filter;

		if (!refiner) {
			console.warn(`Filter [${filter}] does not exist.`);
			return;
		}

		router.reload({
			...defaultOptions,
			...options,
			data: {
				[refiner.name]: pipe(value),
			},
		});
	}

	/**
	 * Applies the given sort.
	 */
	function applySort(
		sort: Sort | string,
		direction: Direction = null,
		options: VisitOptions = {},
	) {
		const refiner = typeof sort === "string" ? getSort(sort, direction) : sort;

		if (!refiner) {
			console.warn(`Sort [${sort}] does not exist.`);
			return;
		}

		router.reload({
			...defaultOptions,
			...options,
			data: {
				[refinements.value.config.sort]: omitValue(refiner.next),
			},
		});
	}

	/**
	 * Applies a text search.
	 */
	function applySearch(
		value: string | null | undefined,
		options: VisitOptions = {},
	) {
		value = [stringValue, omitValue].reduce(
			(result, transform) => transform(result),
			value,
		);

		router.reload({
			...defaultOptions,
			...options,
			data: {
				[refinements.value.config.search]: value,
			},
		});
	}

	/**
	 * Applies the given match.
	 */
	function applyMatch(search: Search | string, options: VisitOptions = {}) {
		const refiner = typeof search === "string" ? getSearch(search) : search;

		if (!refiner) {
			console.warn(`Match [${search}] does not exist.`);
			return;
		}

		const matches = toggleValue(
			refiner.name,
			currentSearches.value.map(({ name }) => name),
		);

		router.reload({
			...defaultOptions,
			...options,
			data: {
				[refinements.value.config.match]: delimitArray(matches),
			},
		});
	}

	/**
	 * Clear the given filter.
	 */
	function clearFilter(filter?: Filter | string, options: VisitOptions = {}) {
		if (filter) {
			applyFilter(filter, undefined, options);
			return;
		}

		const data = Object.fromEntries(
			currentFilters.value.map(({ name }) => [name, undefined]),
		);

		router.reload({
			...defaultOptions,
			...options,
			data,
		});
	}

	/**
	 * Clear the sort.
	 */
	function clearSort(options: VisitOptions = {}) {
		router.reload({
			...defaultOptions,
			...options,
			data: {
				[refinements.value.config.sort]: undefined,
			},
		});
	}

	/**
	 * Clear the search.
	 */
	function clearSearch(options: VisitOptions = {}) {
		applySearch(undefined, options);
	}

	/**
	 * Clear the matching columns.
	 */
	function clearMatch(options: VisitOptions = {}) {
		if (!refinements.value.config.match) {
			console.warn("Matches key is not set.");
			return;
		}

		router.reload({
			...defaultOptions,
			...options,
			data: {
				[refinements.value.config.match]: undefined,
			},
		});
	}

	/**
	 * Resets all filters, sorts, matches and search.
	 */
	function reset(options: VisitOptions = {}) {
		router.reload({
			...defaultOptions,
			...options,
			data: {
				[refinements.value.config.search]: undefined,
				[refinements.value.config.sort]: undefined,
				[refinements.value.config.match]: undefined,
				...Object.fromEntries(
					refinements.value.filters?.map((filter) => [
						filter.name,
						undefined,
					]) ?? [],
				),
			},
		});
	}

	/**
	 * Binds a filter to a form input.
	 */
	function bindFilter<T extends any>(
		filter: Filter | string,
		options: BindingOptions = {},
	) {
		const refiner = typeof filter === "string" ? getFilter(filter) : filter;

		if (!refiner) {
			console.warn(`Filter [${filter}] does not exist.`);
			return;
		}

		const value = refiner.value as T;

		const {
			debounce = 250,
			transform = (value: any) => value,
			...visitOptions
		} = options;

		return {
			"onUpdate:modelValue": useDebounceFn((value: any) => {
				applyFilter(refiner, transform(value), visitOptions);
			}, debounce),
			modelValue: value,
		};
	}

	/**
	 * Binds a sort to a button.
	 */
	function bindSort(sort: Sort | string, options: BindingOptions = {}) {
		const refiner = typeof sort === "string" ? getSort(sort) : sort;

		if (!refiner) {
			console.warn(`Sort [${sort}] does not exist.`);
			return;
		}

		const { debounce = 0, transform, ...visitOptions } = options;
		return {
			onClick: useDebounceFn(() => {
				applySort(refiner, currentSort.value?.direction, visitOptions);
			}, debounce),
		};
	}

	/**
	 * Binds a search input to a form input.
	 */
	function bindSearch(options: BindingOptions = {}) {
		const { debounce = 700, transform, ...visitOptions } = options;

		return {
			"onUpdate:modelValue": useDebounceFn(
				(value: string | null | undefined) => {
					applySearch(value, visitOptions);
				},
				debounce,
			),
			modelValue: refinements.value.config.term ?? "",
		};
	}

	/**
	 * Binds a match to a checkbox.
	 */
	function bindMatch(match: Search | string, options: BindingOptions = {}) {
		const refiner = typeof match === "string" ? getSearch(match) : match;

		if (!refiner) {
			console.warn(`Match [${match}] does not exist.`);
			return;
		}

		const { debounce = 0, transform, ...visitOptions } = options;

		return {
			"onUpdate:modelValue": useDebounceFn((value: any) => {
				applyMatch(value, visitOptions);
			}, debounce),
			modelValue: isSearching(refiner),
			value: refiner.name,
		};
	}

	return {
		filters,
		sorts,
		searches,
		getFilter,
		getSort,
		getSearch,
		currentFilters,
		currentSort,
		currentSearches,
		isFiltering,
		isSorting,
		isSearching,
		apply,
		applyFilter,
		applySort,
		applySearch,
		applyMatch,
		clearFilter,
		clearSort,
		clearSearch,
		clearMatch,
		reset,
		bindFilter,
		bindSort,
		bindSearch,
		bindMatch,

		stringValue,
		omitValue,
		toggleValue,
		delimitArray,
	};
}
