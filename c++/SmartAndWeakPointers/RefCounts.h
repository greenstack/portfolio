#pragma once

namespace Origin {
	namespace Core {
		typedef size_t PtrCount;

		/**
		 * Contains the number of strong and weak pointer references.
		 */
		struct RefCounts {
			// The number of strong pointer references.
			PtrCount strongPtrs;
			// The number of weak pointer references.
			PtrCount weakPtrs;
			/**
			 * Creates a reference count object with 0 references.
			 */
			RefCounts() :
				strongPtrs(0),
				weakPtrs(0)
			{}
			/**
			 * Creates a reference count object.
			 * @param strPtrs The number of smartPtrs point to the object.
			 * @param wkPtrs The number of weakPtrs pointing to the object.
			 */
			RefCounts(PtrCount strPtrs, PtrCount wkPtrs) :
				strongPtrs(strPtrs),
				weakPtrs(wkPtrs)
			{}
			RefCounts(RefCounts&&) = delete;
		};
	}
}
