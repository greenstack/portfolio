#pragma once

namespace Origin {
	namespace Memory {
		struct BlockDescriptor {
			BlockDescriptor* previous;
			BlockDescriptor* next;
			void* m_pBlockStartAddr;
			size_t m_pBlockSize;

			BlockDescriptor(void* addr, size_t blockSize) : 
				m_pBlockStartAddr(addr),
				m_pBlockSize(blockSize),
				previous(nullptr),
				next(nullptr)
			{}

			void insertBefore(BlockDescriptor* after);
		};
	}
}