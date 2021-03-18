#pragma once
#include "SmartPtr.h"

namespace Origin {
	namespace Core {
		//template <typename T> class SmartPtr;

		/**
		 * Contains a weak reference to a pointer.
		 * @type-param T the type of the object referred to by the pointer.
		 */
		template<typename T>
		class WeakPtr
		{
			template <typename T> friend class SmartPtr;
		public:
			/**
			 * Generates a weak reference to an object.
			 *
			 * @param target The smart pointer to create a reference to.
			 */
			explicit WeakPtr(const SmartPtr<T>& target) :
				object(target.object),
				refs(target.refs)
			{
				if (refs) refs->weakPtrs++;
			}

			/**
			 * Creates a weak pointer from another.
			 *
			 * @param other The other weak pointer.
			 */
			WeakPtr(const WeakPtr<T>& other) :
				object(other.object),
				refs(other.refs)
			{
				if (refs) refs->weakPtrs++;
			}

			/** 
			 * Releases this pointer's reference to the object.
			 */
			~WeakPtr() {
				Release();
			}

			/**
			 * Gets the number of weak references to the object.
			 *
			 * @return The number of weak references to the object.
			 */
			PtrCount weakUses() const {
				return refs ? refs->weakPtrs : 0;
			}

			/**
			 * Assigns the weakPtr on the left to the one on the right.
			 *
			 * @param other The weak pointer being copied over
			 */
			WeakPtr& operator=(const WeakPtr<T>& other) {
				Release();
				object = other.object;
				refs = other.refs;
				if (refs) refs->weakPtrs++;
				return *this;
			}

			/**
			 * Checks if the stored object is available.
			 *
			 * @return True if the object has not been destroyed.
			 */
			operator bool() const { return refs ? refs->strongPtrs > 0 : false; }

			/**
			 * Promotes a weak pointer into a smart pointer.
			 *
			 * @param other The weak pointer to promote.
			 *
			 * @return The promoted smart pointer.
			 */
			template<class T>
			static SmartPtr<T> promote(const WeakPtr<T>& other) {
				return other.promote();
			}

			/**
			 * Promotes this weak pointer to a strong pointer.
			 */
			SmartPtr<T> promote() const {
				return SmartPtr<T>(*this);
			}

			bool operator==(const WeakPtr<T>& right) const;
			bool operator==(const std::nullptr_t right) const;
			bool operator!=(const WeakPtr<T>& right) const;
			bool operator!=(const std::nullptr_t right) const;
		private:
			/**
			 * Releases the weak reference to the pointer. If there are no more
			 * smart or weak references to the pointer after release, the
			 * reference counter is deleted.
			 *
			 * @see SmartPtr<T>::Release
			 */
			void Release();
			T* object;
			RefCounts* refs;
		};
	}
}

#include "WeakPtr-inl.h"
