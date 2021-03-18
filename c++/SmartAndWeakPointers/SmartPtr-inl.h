#include "SmartPtr.h"
#pragma once

template<typename T>
inline bool Origin::Core::SmartPtr<T>::operator==(const SmartPtr<T>& right) const
{
	if (this == &right) return true;
	return object == right.object;
}

template<typename T>
inline bool Origin::Core::SmartPtr<T>::operator==(const std::nullptr_t right) const
{
	return object == nullptr;
}

template<typename T>
inline bool Origin::Core::SmartPtr<T>::operator!=(const SmartPtr<T>& right) const
{
	if (this == &right) return false;
	return object != right.object;
}

template<typename T>
inline bool Origin::Core::SmartPtr<T>::operator!=(const std::nullptr_t right) const
{
	return object != nullptr;
}

template<typename T>
inline void Origin::Core::SmartPtr<T>::Release() {
	if (refs) {
		if (--(refs->strongPtrs) == 0 && object) {
			delete object;
		}

		// If no more references are held, then we want to delete the counter
		if (refs->strongPtrs == 0 && refs->weakPtrs == 0) {
			delete refs;
		}
	}
}
