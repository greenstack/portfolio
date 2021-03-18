#include "WeakPtr.h"
#pragma once


template<typename T>
inline bool Origin::Core::WeakPtr<T>::operator==(const WeakPtr<T>& right) const
{
	if (this == &right) return true;
	return object == right.object;
}

template<typename T>
inline bool Origin::Core::WeakPtr<T>::operator==(const std::nullptr_t right) const
{
	return refs->strongPtrs == 0;
}

template<typename T>
inline bool Origin::Core::WeakPtr<T>::operator!=(const WeakPtr<T>& right) const
{
	if (this != &right) return true;
	return object != right.object;
}

template<typename T>
inline bool Origin::Core::WeakPtr<T>::operator!=(const std::nullptr_t right) const
{
	return refs->strongPtrs < 1;
}

template<typename T>
inline void Origin::Core::WeakPtr<T>::Release() {
	if (!refs) return;
	(refs->weakPtrs)--;
	if (refs->weakPtrs == 0 && refs->strongPtrs == 0) {
		delete refs;
	}
}
