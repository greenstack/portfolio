#include <Windows.h>
#include <assert.h>
#include <stdio.h>
#include "HeapManager.h"
#include "BlockDescriptor.h"

#define SUM_ADDRESS(address, modby) static_cast<uint8_t*>(address) + (modby)

namespace Origin { 
	namespace Memory {

#ifdef _DEBUG
		// 0xFEEDBEEF;
		constexpr uint8_t PRE_GUARD_BYTE_FIRST = 0xFE;
		constexpr uint8_t PRE_GUARD_BYTE_SECOND = 0xED;
		constexpr uint8_t PRE_GUARD_BYTE_THIRD = 0xBE;
		constexpr uint8_t PRE_GUARD_BYTE_LAST = 0xEF;

		// 0x1A571EAF
		constexpr uint8_t POST_GUARD_BYTE_FIRST = 0x1A;
		constexpr uint8_t POST_GUARD_BYTE_SECOND = 0x57;
		constexpr uint8_t POST_GUARD_BYTE_THIRD = 0x1E;
		constexpr uint8_t POST_GUARD_BYTE_LAST = 0xAF;
		
		constexpr size_t BAND_SIZE = 8;
#endif
		constexpr uint8_t LANDFILL = 0xAA;
		constexpr uint8_t FREEFILL = 0xDD;

		HeapManager & HeapManager::resize(void* heap, size_t size)
		{
			HeapManager& instance = getInstance();
			// Can't resize if we've got data in here
			assert(!instance.allocatedBlocks);
			instance.destroy();
			instance.heap = heap;
			instance.size = size;
			*static_cast<BlockDescriptor*>(instance.heap) = BlockDescriptor(SUM_ADDRESS(instance.heap, sizeof(BlockDescriptor)), size - sizeof(BlockDescriptor));
			instance.availableBlocks = reinterpret_cast<BlockDescriptor*>(instance.heap);
			return instance;
		}

		HeapManager & HeapManager::resize(size_t size) {
			return resize(getPage(size), size);
		}

		HeapManager& HeapManager::getInstance() {
			static HeapManager instance;

			return instance;
		}

		HeapManager::~HeapManager()
		{
			destroy();
		}

		void* HeapManager::getPage(size_t size) {
			SYSTEM_INFO SysInfo;
			GetSystemInfo(&SysInfo);
			// round our size to a multiple of memory page size
			assert(SysInfo.dwPageSize > 0);
			size_t sizeHeapInPageMultiples = SysInfo.dwPageSize * ((size + SysInfo.dwPageSize) / SysInfo.dwPageSize);

			void * heap = VirtualAlloc(NULL, sizeHeapInPageMultiples, MEM_RESERVE | MEM_COMMIT, PAGE_READWRITE);
			assert(heap);
			return heap;
		}

		void* HeapManager::alloc(size_t size) {
			// Make sure that the memory's aligned
#ifdef _DEBUG
			size_t baseSize = size;
#endif
			size_t alignment = size % 4;
			if (alignment) size += (4 - alignment);
#ifdef _DEBUG
			size += BAND_SIZE;
#endif
			BlockDescriptor* availableBlock = availableBlocks;
			// Iterate through the BlockDescriptors to find the first available
			// block with enough memory
			if (!availableBlock) return nullptr;
			do {
				if (size <= availableBlock->m_pBlockSize) break;
			} while (availableBlock = availableBlocks->next);
			// We couldn't find enough, so we return nullptr.
			if (availableBlock == nullptr) return nullptr;
			

			// Allocate the memory
			uintptr_t startAddr = reinterpret_cast<uintptr_t>(availableBlock->m_pBlockStartAddr);
			
			//vvvvvvvvvvvvvvvv REALIGN AVAILABLE BLOCK LIST vvvvvvvvvvvvvvvv//
			size_t requiredSpace = size + sizeof(BlockDescriptor);
			if (requiredSpace < availableBlock->m_pBlockSize) {
				// Put the new block descriptor after the allocated block
				BlockDescriptor* shortenedBlock = reinterpret_cast<BlockDescriptor*>(startAddr + size);
				*shortenedBlock = BlockDescriptor(reinterpret_cast<void*>(startAddr + requiredSpace), availableBlock->m_pBlockSize - requiredSpace);
				// Insert the shortened block into the available list
				shortenedBlock->insertBefore(availableBlock);
				if (availableBlock == availableBlocks) availableBlocks = shortenedBlock;
				availableBlock->m_pBlockSize = size;
			}
			else {
				// Remove the available block from its spot in the list
				if (availableBlock->previous)
					availableBlock->previous->next = availableBlock->next;
				if (availableBlock->next)
					availableBlock->next->previous = availableBlock->previous;
				if (availableBlock == availableBlocks) {
					availableBlocks = availableBlock->next;
				}
				
			}
			//^^^^^^^^^^^^^^^^ REALIGN AVAILABLE BLOCK LIST ^^^^^^^^^^^^^^^^//

#ifdef _DEBUG
			startAddr += BAND_SIZE / 2;
#endif
			BlockDescriptor* allocatedBlock = availableBlock;
			allocatedBlock->m_pBlockStartAddr = reinterpret_cast<void*>(startAddr);
			//allocatedBlock->m_pBlockSize = size;

			if (allocatedBlocks) {
				allocatedBlocks->next = allocatedBlock;
				allocatedBlock->previous = allocatedBlocks;
			}
			// Unset the pointer to the next allocated block
			allocatedBlock->next = nullptr;
			allocatedBlocks = allocatedBlock;

#ifdef _DEBUG // Guardbanding and debugging
			uint8_t* writeAt = reinterpret_cast<uint8_t*>(availableBlock->m_pBlockStartAddr) - BAND_SIZE / 2;
			guardband(writeAt, PRE_GUARD_BYTE_FIRST, PRE_GUARD_BYTE_SECOND, PRE_GUARD_BYTE_THIRD, PRE_GUARD_BYTE_LAST);
			// Fill up the memory with debug data (LANDFILL)
			fill(writeAt, LANDFILL, baseSize);
			guardband(writeAt, POST_GUARD_BYTE_FIRST, POST_GUARD_BYTE_SECOND, POST_GUARD_BYTE_THIRD, POST_GUARD_BYTE_LAST);
#endif //_DEBUG;
			
			//allocatedBlock->next = nullptr;
			// Update the available block we just took from
			return allocatedBlock->m_pBlockStartAddr;
		}

		void HeapManager::free(void* ptr) {
			// Find the block descriptor
			BlockDescriptor* block = allocatedBlocks;
			// We can't free memory if there's nothing to free
			assert(block);
			do {
				if (block->m_pBlockStartAddr == ptr) break;
			} while (block = block->previous);
			assert(block);

			if (block == allocatedBlocks) {
				allocatedBlocks = block->previous;
			}

			// Realign the allocated list by removing all references 
			if (block->next) block->next->previous = block->previous;
			if (block->previous) block->previous->next = block->next;
			
#ifdef _DEBUG
			// Since Debug is enabled, we'll want to reset the start address
			block->m_pBlockStartAddr = static_cast<uint8_t*>(block->m_pBlockStartAddr) - (BAND_SIZE / 2);
			//block->m_pBlockSize += BAND_SIZE / 2;
#endif //_DEBUG

#ifdef _DEBUG
			// Fill the freed memory with debug-related info
			uint8_t* writeAt = reinterpret_cast<uint8_t*>(block->m_pBlockStartAddr);
			fill(writeAt, FREEFILL, block->m_pBlockSize);
#endif //_DEBUG
			
			// Clear the links for when we insert them...?
			insertAvailableBlock(block);
			coallesce();
		}

		void HeapManager::coallesce() {
			BlockDescriptor* currentBlock = availableBlocks;
			if (!currentBlock) return;
			while (BlockDescriptor* next = currentBlock->next) {
				
				uintptr_t endAddr = reinterpret_cast<uintptr_t>(currentBlock->m_pBlockStartAddr);
				endAddr = endAddr + currentBlock->m_pBlockSize;
				// Blocks align; coallesce them
				if (endAddr == reinterpret_cast<uintptr_t>(next)) {
					// Merge the sizes, with the size of a BlockDescriptor as well
					currentBlock->m_pBlockSize += next->m_pBlockSize;
					currentBlock->m_pBlockSize += sizeof(BlockDescriptor);
					
					// Remove the next block from the list
					currentBlock->next = next->next;
					if (next->next)
						next->next->previous = currentBlock;
#ifdef _DEBUG // Show that the data has been merged
					uint8_t* writeAt = reinterpret_cast<uint8_t*>(next);
					fill(writeAt, FREEFILL, next->m_pBlockSize + sizeof(BlockDescriptor));
#endif //_DEBUG
					continue; //Check the next block to see if we should coallesce
				}
				assert(availableBlocks->previous == nullptr);
				currentBlock = currentBlock->next;
			}
		}

		bool HeapManager::contains(void* ptr) const {
			uintptr_t lowr = reinterpret_cast<uintptr_t>(heap);
			uintptr_t addr = reinterpret_cast<uintptr_t>(ptr);
			uintptr_t uppr = lowr + size;
			return lowr <= addr && addr <= uppr;
		}

		bool HeapManager::isAllocated(void* ptr) const {
			BlockDescriptor* current = allocatedBlocks;
			if (!current) return false;
			do {
				if (current->m_pBlockStartAddr == ptr) return true;
			} while (current = current->previous);
			return false;
		}

		size_t HeapManager::availableMemory() const {
			size_t available = 0;
			BlockDescriptor* current = availableBlocks;
			if (!current) return available;
			do {
				available += current->m_pBlockSize;
			} while (current = current->next);
			return available;
		}

		size_t HeapManager::largestAvailableBlock() const {
			size_t largest = 0;
			BlockDescriptor* current = availableBlocks;
			if (!current) return 0;
			do {
				largest = largest > current->m_pBlockSize ? largest : current->m_pBlockSize;
			} while (current = current->next);
			return largest;
		}

		void HeapManager::destroy() {
			VirtualFree(heap, 0, MEM_RELEASE);
			size = 0;
			availableBlocks = allocatedBlocks = nullptr;
		}

		void HeapManager::insertAvailableBlock(BlockDescriptor* avail) {
			BlockDescriptor* current = availableBlocks;
			do {
				if (avail->m_pBlockStartAddr < current->m_pBlockStartAddr) {
					
					avail->previous = current->previous;
					if (current->previous)
						current->previous->next = avail;
					current->previous = avail;
					avail->next = current;
					
					if (current == availableBlocks) {
						availableBlocks = avail;
					}
					assert(availableBlocks->previous == nullptr);
					return;
				}
			} while (current = current->next);
			// The address is after all blocks, so we go ahead and put it at
			// the end of the list.
			if (current)
				current->next = avail;
			//else
				//current = avail;
		}

		void HeapManager::displayMemory(size_t per_row) const {
			void* cur_addr = heap;
			printf("+++ Start mem dump +++\n");
			while ((unsigned long long)cur_addr - (unsigned long long)heap < MAX_HEAP_DISPLAY_SIZE) {
				printf("%4.4p: ", cur_addr);
				for (size_t i = 0; i < per_row; i++) {
					void* column = static_cast<uint8_t*>(cur_addr) + i;
					printf("%2.2x ", *static_cast<uint8_t*>(column) & 0xff);
				}
				cur_addr = static_cast<uint8_t*>(cur_addr) + per_row;
				printf("\n");
			}
			BlockDescriptor* current = availableBlocks;
			size_t available = 0;
			while (current) {
				available += current->m_pBlockSize;
				current = current->next;
			}
			printf("%zu bytes available; ", available);
			current = allocatedBlocks;
			size_t allocated  = 0;
			while (current) {
				allocated += current->m_pBlockSize;
				current = current->previous;
			}
			printf("%zu bytes in use\n", allocated);
			printf("--- End mem dump ---\n");
		}

#ifdef _DEBUG
		void HeapManager::fill(uint8_t*& writeAt, uint8_t byte, size_t bytes_to_write) {
			for (size_t i = 0; i < bytes_to_write; ++i) {
				*writeAt++ = byte;
			}
		}

		void HeapManager::guardband(uint8_t*& writeAt, uint8_t b1, uint8_t b2, uint8_t b3, uint8_t b4) {
			*writeAt++ = b1;
			*writeAt++ = b2;
			*writeAt++ = b3;
			*writeAt++ = b4;
		}
#endif
	} // namespace Memory
} //namespace Origin
