#pragma once
#include <stdint.h>	

#include "BlockDescriptor.h"

#ifdef _DEBUG
#define MEM_DUMP() Origin::Memory::HeapManager::getInstance().displayMemory(0x20)
#else
#define MEM_DUMP() void(0)
#endif // _DEBUG

constexpr size_t HEAP_MANAGER_SIZE = 1024 * 1024;

namespace Origin {
	namespace Memory {
		class HeapManager
		{
		public:
			// Resizes the heap manager from the default size to the desired size.
			// Fails if the manager has any allocations in it.
			static HeapManager& resize(size_t size);
			// Resizes the heap manager from the default size to the desired size
			// with the provided heap. Fails if the manager has any allocations 
			// in it.
			static HeapManager& resize(void* heap, size_t size);
			static HeapManager& getInstance();
			const size_t MAX_HEAP_DISPLAY_SIZE = 512; //1064 * 1064;
			~HeapManager();
			void* alloc(size_t size);
			void free(void* at);
			void coallesce();
			bool contains(void* ptr) const;
			bool isAllocated(void* ptr) const;
			size_t availableMemory() const;
			size_t largestAvailableBlock() const;
			void destroy();
			void displayMemory(size_t per_row) const;
			HeapManager(HeapManager&) = delete;
			void operator=(HeapManager const&) = delete;
		private:
			HeapManager() :
				heap(getPage(HEAP_MANAGER_SIZE)),
				size(HEAP_MANAGER_SIZE),
				availableBlocks(static_cast<BlockDescriptor*>(heap)),
				allocatedBlocks(nullptr)
			{
				uintptr_t startAddr = reinterpret_cast<uintptr_t>(heap) + sizeof(BlockDescriptor);
				*availableBlocks = BlockDescriptor(reinterpret_cast<void*>(startAddr), size - sizeof(BlockDescriptor));
			}
			static void* getPage(size_t size);
			void* heap;
			// This always points to the first available block.
			BlockDescriptor* availableBlocks;
			// This points to the most recent block that was allocated.
			BlockDescriptor* allocatedBlocks;
			
			size_t size;

			void insertAvailableBlock(BlockDescriptor* avail);
#ifdef _DEBUG
			void fill(uint8_t*& writeAt, uint8_t byte, size_t bytes_to_write);
			void guardband(uint8_t*& writeAt, uint8_t b1, uint8_t b2, uint8_t b3, uint8_t b4);
#endif
		};
	}
}

