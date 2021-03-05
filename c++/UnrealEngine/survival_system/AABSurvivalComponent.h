#pragma once

#include "CoreMinimal.h"
#include "Components/ActorComponent.h"
#include "ABSurvivalStats.h"
#include "GameBase/Define.h"
#include "ABStatModifierInterface.h"
#include "AABSurvivalComponent.generated.h"

// Forward declares
class UAABSurvivalComponent;
class AABAnimalCharacter;

/**
* Struct for the FStatModifiersChanged event.
*/
USTRUCT(Blueprintable, Category = "Character | Survival | Events")
struct FStatModifierChangedInfo {
	GENERATED_BODY()
public:
	/**
	 * Default constructor.
 	 */
	FStatModifierChangedInfo() {}

	/**
	 * Constructs the modifier changed info struct with the survival component and stat modifier already populated.
	 *
	 * @param sender
	 *   The survival component that's getting the modifier added to it.
	 * @param modifier
	 *   The modifier being added to or removed from the survival component.
	 */
	FStatModifierChangedInfo(UAABSurvivalComponent* sender, IABStatModifierInterface* modifier) : SurvivalComponent(sender) {
		StatModifier.SetInterface(modifier);
	}

	/**
	 * The survival component that's been changed.
	 */
	UPROPERTY(BlueprintReadonly, Category = "Character | Survival | Events")
	UAABSurvivalComponent* SurvivalComponent;
	
	/**
	 * The stat modifier that's been added or removed from the survival component.
	 */
	UPROPERTY(BlueprintReadonly, Category = "Character | Survival | Events")
	TScriptInterface<IABStatModifierInterface> StatModifier;
};

/**
* Delegate for when a stat modifier is added or removed.
*/
DECLARE_DYNAMIC_MULTICAST_DELEGATE_OneParam(FStatModifiersChanged, const FStatModifierChangedInfo&, info);

/**
* Struct for the FAnimalCriticalConditionChanged event.
*/
USTRUCT(Blueprintable, Category = "Character | Survival | Events")
struct FAnimalCriticalConditionChangedInfo {
	GENERATED_BODY()
public:
	/**
	 * Default constructor for FAnimalCriticalConditionChangedInfo.
	 */
	FAnimalCriticalConditionChangedInfo() {}

	/**
	 * Builds the information for the critical status change automatically.
	 *
	 * @param animal
	 *   The animal whose critical condition has changed.
	 * @param inCriticalCondition
	 *   Is the animal in critical condition?
	 */
	FAnimalCriticalConditionChangedInfo(AABAnimalCharacter* animal, bool inCriticalCondition) :
		Owner(animal),
		IsNowInCriticalCondition(inCriticalCondition)
	{}

	/**
	 * A reference to the animal who's condition has changed.
	 */
	UPROPERTY(BlueprintReadonly, Category = "Character | Survival | Events")
	AABAnimalCharacter* Owner;

	/**
	 * With this event, did the animal enter critical condition?
	 */
	UPROPERTY(BlueprintReadonly, Category = "Character | Survival | Events")
		bool IsNowInCriticalCondition;
};

/**
* Delegate for when an animal enters or leaves critical condition.
*/
DECLARE_DYNAMIC_MULTICAST_DELEGATE_TwoParams(FAnimalCriticalConditionChanged, UAABSurvivalComponent*, sender, const FAnimalCriticalConditionChangedInfo&, info);

UCLASS( Blueprintable, ClassGroup=(Custom), meta=(BlueprintSpawnableComponent) )
class AHRIANDBEAR_API UAABSurvivalComponent : public UActorComponent
{
	GENERATED_BODY()

public:	
	// Sets default values for this component's properties
	UAABSurvivalComponent();

	/**
	 * The animal's thirst stat.
	 */
	UPROPERTY(EditAnywhere, BlueprintReadonly, Category = "Character | Survival")
	FABSurvivalStat Thirst;

	/**
	 * The animal's hunger stat.
	 */
	UPROPERTY(EditAnywhere, BlueprintReadonly, Category = "Character | Survival")
	FABSurvivalStat Hunger;

	/**
	 * Updates each of the stats.
	 *
	 * @param deltaTime
	 * 	 The amount of time elapsed since the last update.
	 */
	void UpdateStats(float deltaTime);

	/**
	 * This delegate is fired whenever the animal has a stat modifier added.
	 */
	UPROPERTY(BlueprintAssignable, Category = "Character | Survival | Events")
	FStatModifiersChanged StatModifierAdded;

	/**
	 * This delegate is fired whenever the animal has a stat modifier removed.
	 */
	UPROPERTY(BlueprintAssignable, Category = "Character | Survival | Events")
	FStatModifiersChanged StatModifierRemoved;

	/**
	 * This delegate triggers whenever the critical condition state changes.
	 *
	 * Critical condition is defined as whenever both the hunger and thirst
	 * stats are zero or lower.
	 */
	UPROPERTY(BlueprintAssignable, Category = "Character | Survival | Events")
	FAnimalCriticalConditionChanged OnCriticalConditionChanged;

	/**
	* Is the animal in critical condition?
	*
	* @return
	*   TRUE if both thirst and hunger are at zero or lower.
	*/
	UFUNCTION(BlueprintPure, Category = "Character | Survival")
	bool IsInCriticalCondition() const { return zeroedStats >= RequiredSurvivalStats; }

protected:
	// Called when the game starts
	virtual void BeginPlay() override;

public:	
	// Called every frame
	virtual void TickComponent(float DeltaTime, ELevelTick TickType, FActorComponentTickFunction* ThisTickFunction) override;

	FORCEINLINE FSurvivalData GetSurvivalData() const { return FSurvivalData{ Health.CurrentValue, Hunger.CurrentValue, Thirst.CurrentValue, Warmth.CurrentValue }; }
	void AddSurvivalData(const FSurvivalData& value);

	/**
	 * Adds a stat modifier to the animal's survival component.
	 *
	 * @param modifier
	 *   The modifier to add to animal's survival component.
	 */
	void AddModifier(IABStatModifierInterface* modifier);

	/**
	 * Removes the stat modifier from the animal's survival component.
	 *
	 * @param modifier
	 *   The modifier to remove from the animal's survival component.
	 */
	void RemoveModifier(IABStatModifierInterface* modifier);

private:

	void UpdateRateOfChange(FABSurvivalStat& stat, const float defaultRoC, float(IABStatModifierInterface::*statModMethod)(UAABSurvivalComponent*, float, float), bool (IABStatModifierInterface::*doesModMethod)(void) const);

	/**
	 * Responds to the OnStatZeroStateChanged events on Hunger and Thirst.
	 *
	 * @param stateChangedInfo
	 *   The information regarding the change in state of the animal's condition.
	 */
	UFUNCTION()
	void RespondToStatZeroedStateChange(const FStatZeroedStateChangedInfo& stateChangeInfo);
	
	/**
	 * The stat modifiers currently being applied to the survival component.
	 */
	TArray<IABStatModifierInterface*> StatModifiers;

	/**
	 * The default rate of change (in units/second) for the animal's hunger.
	 */
	UPROPERTY(VisibleAnywhere, Category = "Character | Survival")
	float defaultHungerRateOfChange;

	/**
	 * The default rate of change (in units/second) for the animal's thirst.
	 */
	UPROPERTY(VisibleAnywhere, Category = "Character | Survival")
	float defaultThirstRateOfChange;

	/**
	 * Represents the number of stats that need to be zeroed out.
	 */
	const int RequiredSurvivalStats = 2;

	int zeroedStats = 0;
};
