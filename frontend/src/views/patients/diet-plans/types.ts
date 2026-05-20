export interface NutritionalGoals {
  protein_g: number
  carbs_g: number
  fat_g: number
}

export interface DietDay {
  day: string
  breakfast: string
  lunch: string
  dinner: string
  snack: string
}

export interface GeneratedBy {
  id: string
  name: string
}

export interface DietPlanSummary {
  id: string
  status: 'pending' | 'completed' | 'failed'
  dailyCalories: number | null
  nutritionalGoals: NutritionalGoals | null
  warnings: string[] | null
  failureReason: string | null
  generatedBy?: GeneratedBy
  createdAt: string
}

export interface DietPlan extends DietPlanSummary {
  rationale: string | null
  days: DietDay[] | null
}
