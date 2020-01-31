#pragma once
#include "BlockDescriptor.h"

namespace Origin {
	namespace Memory {
		void BlockDescriptor::insertBefore(BlockDescriptor* after) {
			previous = after->previous;
			if (after->previous) {
				after->previous->next = this;
			}
			next = after->next;
			if (after->next) {
				after->next->previous = this;
			}
		}
	}
}
