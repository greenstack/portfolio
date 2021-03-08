// Fill out your copyright notice in the Description page of Project Settings.

#pragma once

#include "CoreMinimal.h"
#include "UObject/Interface.h"
#include "ABStatModifierInterface.generated.h"

// Forward declares
class UAABSurvivalComponent;

// This class does not need to be modified.
UINTERFACE(MinimalAPI)
class UABStatModifierInterface : public UInterface
{
	GENERATED_BODY()
};

/**
 * 
 */
class AHRIANDBEAR_API IABStatModifierInterface
{
	GENERATED_BODY()

		// Add interface functions to this class. This is the class that will be inherited to implement this interface.
public:
	/**
	* @return The priority of this modifier. Higher priorities are counted later.
	*/
	int GetPriority() const { return priority; }

	/**
	* Does this particular modifier change the rate of hunger change?
	*
	* If this returns false, GetHungerRateModifier is not called.
	*
	* @return True if this modifier changes hunger.
	*/
	virtual bool DoesModifyHungerRate() const PURE_VIRTUAL("Please", { return 0; });
	
	/**
	* Gets the modified value of the thirst rate.
	*
	* @param mainComp
	*   The component being modified.
	* @param defaultThirstDelta
	*   The default rate of change for hunger.
	* @param currentDelta
	*   The current rate of change for hunger.
	*
	* @return float
	*   The new hunger change rate.
	*/
	virtual float GetHungerRateModifier(UAABSurvivalComponent* mainComp, float defaultHungerDelta, float currentDelta) PURE_VIRTUAL("Please", { return 0; });

	/**
	* Does this particular modifier change the rate of thirst change?
	*
	* If this returns false, GetThirstRateModifier should not be called.
	*
	* @return True if this modifier changes hunger.
	*/
	virtual bool DoesModifyThirstRate() const PURE_VIRTUAL("Please", { return 0; });
	
	/**
	* Gets the modified value of the thirst rate.
	*
	* @param mainComp
	*   The component being modified.
	* @param defaultThirstDelta
	*   The default rate of change for thirst.
	* @param currentDelta
	*   The current rate of change for thirst.
	*
	* @return float
	*   The new thirst change rate.
	*/
	virtual float GetThirstRateModifier(UAABSurvivalComponent* mainComp, float defaultThirstDelta, float currentDelta) PURE_VIRTUAL("Please", { return 0; });

	bool operator<(const IABStatModifierInterface* right) const;
protected:
	int priority = 0;
};

inline bool operator<(const IABStatModifierInterface& left, const IABStatModifierInterface& right) { return left.GetPriority() < right.GetPriority(); }

inline bool IABStatModifierInterface::operator<(const IABStatModifierInterface* right) const { return GetPriority() < right->GetPriority(); }