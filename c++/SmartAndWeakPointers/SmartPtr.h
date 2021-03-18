#pragma once
#include "RefCounts.h"
#include "WeakPtr.h"

namespace Origin {
	namespace Core {
		/**
		 * Contains a strong reference to a pointer.
		 * @type-param T the type of object held by the pointer.
		 */
		template<typename T>
		class SmartPtr
		{
			template <typename T> friend class WeakPtr;
		public:
			SmartPtr() :
				object(nullptr),
				refs(nullptr)
			{}
			/**
			 * Creates a smart pointer pointing to the given object.
			 *
			 * @param pointer The object that should be pointed to. This must
			 *   be an rvalue to prevent potential issues with accessing
			 *   deleted objects.
			 */
			explicit SmartPtr(T*&& pointer) :
				object(pointer),
				refs(new RefCounts(1, 0))
			{}
			
			/**
			 * Retrieves a smart pointer from a weak pointer.
			 *
			 * @param other The weak pointer to begin owning.
			 */
			explicit SmartPtr(const WeakPtr<T>& other) :
				object(other.object),
				refs(other.refs)
			{
				if (other.refs) {
					if (refs->strongPtrs == 0) {
						object = nullptr;
						refs = nullptr;
					}
					else
						refs->strongPtrs++;
				}
			}

			/**
			 * Creates a strong reference to the object from another smart ptr.
			 * @param other The other strong pointer to copy.
			 */
			SmartPtr(const SmartPtr<T>& other) :
				object(other.object),
				refs(other.refs)
			{
				if (other.refs)
					refs->strongPtrs++;
			}

			/**
			 * Allows the smartPtr to support polymorphism.
			 *
			 * @type-param U a type in T's hierarchy.
			 * @param other The smart pointer with an object in T's hierarchy.
			 */
			template<class U>
			SmartPtr(const SmartPtr<U>& other) :
				object(other.object),
				refs(other.refs)
			{
				if (other.refs)
					refs->strongPtrs++;
			}

			/**
			 * Moves the smart pointer to the other.
			 *
			 * @param other The other strong pointer to copy.
			 */
			SmartPtr(SmartPtr<T>&& other) :
				object(other.object),
				refs(other.refs)
			{
				other.object = nullptr;
				other.refs = nullptr;
			}

			/**
			 * Move semantics supporting polymorphism.
			 * 
			 * @type-param U a type that inherits T.
			 * @param other The smartPtr being moved.
			 */
			template<class U>
			SmartPtr(SmartPtr<U>&& other) :
				object(other.object),
				refs(other.refs)
			{
				other.object = nullptr;
				other.refs = nullptr;
			}

			/**
			 * Destroys this smart pointer, releasing the reference to it. If
			 * this is the last reference to the object, it will be destroyed.
			 */
			~SmartPtr() {
				Release();
			}

			/**
			 * Causes this smart pointer to point to the object held in the
			 * other.
			 *
			 * @param other The other smart pointer to copy.
			 */
			SmartPtr& operator=(const SmartPtr& other) {
				// We don't want to accidentally release the object
				if (this == &other) return *this;
				Release();
				object = other.object;
				refs = other.refs;
				(refs->strongPtrs)++;
				return *this;
			}
			/**
			 * Assigns this smart pointer to point to the nullptr.
			 *
			 * @param i_nullptr A nullptr.
			 */
			SmartPtr& operator=(std::nullptr_t i_nullptr) {
				Release();
				object = nullptr;
				refs = nullptr;
				return *this;
			}

			/**
			 * Gets the number of strong references to the object.
			 *
			 * @return The number of strong references to the object.
			 */
			PtrCount smartUses() const {
				return refs->strongPtrs;
			}

			/**
			 * Checks if this smart pointer points to a valid object.
			 * 
			 * @return True if the smart pointer does not point to nullptr.
			 */
			operator bool() const { return object != nullptr; }
			
			/** 
			 * Returns the object held in this smart pointer.
			 *
			 * @return The object held in the smart pointer.
			 */
			T* operator->() const { return object; }
			
			/**
			 * Returns the object held in this smart pointer.
			 *
			 * @return The object held in the smart pointer.
			 */
			T& operator*() const { return *object; }

			/**
			 * Checks if the smart pointer references the same object.
			 *
			 * @param left The left-hand side of the comparison.
			 * @param right The right hand side of the comparison.
			 */
			bool operator==(const SmartPtr<T>& right) const;

			/**
			 * Checks if the smart pointer references nullptr.
			 *
			 * @param left The smart pointer to check against.
			 * @param right nullptr
			 */
			bool operator==(const std::nullptr_t right) const;

			/**
			 * Checks if the smart pointers point to the same object or not.
			 *
			 * @param left The left-hand side of the comparison.
			 * @param right The right-hand side of the comparison.
			 */
			bool operator!=(const SmartPtr<T>& right) const;

			/**
			 * Checks if the smartPtr doesn't reference nullptr.
			 *
			 * @param left The left-hand side of the comparison.
			 * @param right nullptr.
			 */
			bool operator!=(const std::nullptr_t right) const;
		private:
			/**
			 * Releases this smart pointer's reference to the object.
			 */
			void Release();
			T* object;
			RefCounts* refs;
		}; // class SmartPtr
	} // namespace Core
} // namespace Origin

#include "SmartPtr-inl.h"
