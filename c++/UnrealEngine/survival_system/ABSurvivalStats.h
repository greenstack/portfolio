// Fill out your copyright notice in the Description page of Project Settings.

#pragma once

#include "CoreMinimal.h"
#include "Kismet/BlueprintFunctionLibrary.h"
#include "ABSurvivalStats.generated.h"

USTRUCT(Blueprintable, Category = "Character | Survival | Events")
struct FStatZeroedStateChangedInfo {
	GENERATED_BODY()
public:
	FStatZeroedStateChangedInfo() {}
	FStatZeroedStateChangedInfo(const FString& statName, int oldValue, int newValue) :
		StatName(statName),
		OldValue(oldValue),
		NewValue(newValue),
		IsNowZero(newValue <= 0)
	{}

	UPROPERTY(BlueprintReadOnly, Category = "Character | Survival | Events")
	FString StatName;
	UPROPERTY(BlueprintReadOnly, Category = "Character | Survival | Events")
	int OldValue;
	UPROPERTY(BlueprintReadOnly, Category = "Character | Survival | Events")
	int NewValue;
	UPROPERTY(BlueprintReadOnly, Category = "Character | Survival | Events")
	bool IsNowZero;
};

/**
* This delegate is used when stats drop below zero.
*/
DECLARE_DYNAMIC_MULTICAST_DELEGATE_OneParam(FStatZeroStateChanged, const FStatZeroedStateChangedInfo&, Info);

USTRUCT(BlueprintType)
struct FABSurvivalStat {
	GENERATED_BODY()

	FABSurvivalStat();

	UPROPERTY(EditDefaultsOnly, BlueprintReadOnly, Category = "Character | Survival")
	FString StatName;

	/**
	* The maximum value of a stat. This value should not be directly edited.
	*/
	UPROPERTY(EditAnywhere, BlueprintReadOnly, Category = "Character | Survival")
	float MaxValue = 100.f;

	/**
	* The stat's current value. This should NOT be modified directly. (Oh to let
	* blueprints call struct functions so I can have protected/private variables...)
	*/
	UPROPERTY(EditAnywhere, BlueprintReadOnly, Category = "Character | Survival")
	float CurrentValue;

	/**
	* The amount that this stat will change by over time.
	*
	* Negative numbers will reduce the stat and positive numbers will replenish it.
	* During normal execution, this value should NOT be changed. If you want this
	* value to change dynamically, you should use the ABStatModifierInterface and
	* pipeline pattern. See ABRestArea and AABSurvivalComponent for examples.
	*/
	UPROPERTY(EditAnywhere, BlueprintReadWrite, Category = "Character | Survival")
	float RateOfChange = -1.f;

	/**
	* This sets the current value of the stat when play begins.
	*
	* Make this value negative or zero to have CurrentValue set to MaxValue when play begins.
	*/
	UPROPERTY(EditAnywhere, BlueprintReadOnly, Category = "Character | Survival")
	float StartingValue;

	/**
	* This delegate is triggered when CurrentValue equals zero.
	*/
	UPROPERTY(BlueprintAssignable, Category = "Character | Survival | Events")
	FStatZeroStateChanged OnStatZeroStateChanged;
};


/**
 * Provides functions that can be used on SurvivalStats.
 */
UCLASS()
class AHRIANDBEAR_API UABSurvivalStatFunctions : public UBlueprintFunctionLibrary
{
	GENERATED_BODY()
	
public:
	/**
	* Gets the current value of the stat as a percentage (current value / max value).
	* 
	* @param stat
	*   The stat to query.
	* @return
	*   The amount of the stat remaining as a percentage.
	*/
	UFUNCTION(BlueprintPure, Category = "Character | Survival")
	static float GetStatPercentage(const FABSurvivalStat& stat);

	UFUNCTION(BlueprintPure, Category = "Character | Survival")
	static bool IsStatZeroed(const FABSurvivalStat& stat);

	/**
	* Gets the current value of the given stat.
	*
	* @param stat
	*   The stat to check.
	* @return
	*   The stat's current value.
	*/
	UFUNCTION(BlueprintPure, Category = "Character | Survival")
		static float GetCurrentValue(const FABSurvivalStat& stat);

	/**
	* Is the current value of the given stat zero or less?
	*
	* @param stat
	*   The stat to check.
	* @return
	*   TRUE if GetCurrentValue() returns zero or less.
	*/
	UFUNCTION(BlueprintCallable, Category = "Character | Survival")
		static void AddToCurrentValue(FABSurvivalStat& stat, float value);

	/**
	* Gets the max value of the given stat.
	* 
	* @param stat
	*   The stat to check.
	* @return
	*   The stat's max value.
	*/
	UFUNCTION(BlueprintPure, Category = "Character | Survival")
		static float GetMaxValue(const FABSurvivalStat& stat);

	/**
	* Sets the rate of change for this stat.
	* 
	* @param stat
	*   The stat whose rate of change should be modified.
	* @param newRateOfChange
	*   The new rate of change.
	* @return
	*   The new rate of change.
	*/
	UFUNCTION(BlueprintCallable, Category = "Character | Survival")
		static float SetRateOfChange(FABSurvivalStat& stat, float newRateOfChange);

	/**
	* Changes the stat's current value each frame.
	*
	* @param stat
	*   The stat that should be ticked.
	* @param deltaTime
	*   The amount of time that's elapsed since the last frame.
	*/
	static void TickStat(FABSurvivalStat& stat, float deltaTime);

	/**
	* Sets the stat to its initial value.
	*
	* This is called by the AABSurvival component in BeginPlay.
	*
	* @param stat
	*   The stat to initialize.
	* @param statName
	*   The name of the stat.
	*/
	static void StartStat(FABSurvivalStat& stat, FString statName);
};
