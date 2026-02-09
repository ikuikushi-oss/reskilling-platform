<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\LecturePage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        // 2. Teachers with Profiles
        $teacher1 = User::create([
            'name' => '山田 太郎（Web開発）',
            'email' => 'teacher1@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEACHER,
        ]);
        $teacher1->profile()->create([
            'years_of_experience' => 8,
            'specialty_fields' => 'Laravel, React, AWS',
            'skills' => 'PHP, JavaScript, Docker, Git'
        ]);

        $teacher2 = User::create([
            'name' => '鈴木 花子（AI専攻）',
            'email' => 'teacher2@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEACHER,
        ]);
        $teacher2->profile()->create([
            'years_of_experience' => 5,
            'specialty_fields' => 'Python, 機械学習, 生成AIプロンプト',
            'skills' => 'Python, TensorFlow, PyTorch, OpenAI API'
        ]);

        $teacher3 = User::create([
            'name' => '田中 次郎（DXコンサル）',
            'email' => 'teacher3@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEACHER,
        ]);
        $teacher3->profile()->create([
            'years_of_experience' => 12,
            'specialty_fields' => '業務効率化, DX推進, ノーコード開発',
            'skills' => 'Kintone, PowerAutomate, GAS, Slack Bot'
        ]);

        // 3. Companies & Assignments
        // Helper to create company, assign teacher, and create students
        $createCompanyData = function ($name, $status, $teacher, $desc, $startDate) {
            $company = Company::create([
                'name' => $name,
                'status' => $status,
                'teacher_id' => $teacher->id,
                'business_description' => $desc,
                'contract_start_date' => $startDate
            ]);

            // Sync Pivot for Teacher Dashboard access - REMOVED (Now 1:1 HasMany via teacher_id)
            // $teacher->assignedCompanies()->attach($company->id);

            // Add 3 Students
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'name' => $company->name . " 生徒{$i}",
                    'email' => "student_{$company->id}_{$i}@example.com",
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_STUDENT,
                    'company_id' => $company->id,
                ]);
            }
        };

        // Teacher 1 Companies (Active, Finished, Free Trial)
        $createCompanyData('株式会社テックアカデミー', 'active', $teacher1, 'Webアプリケーション開発受託', '2025-10-01');
        $createCompanyData('ネクストイノベーション株式会社', 'finished', $teacher1, '自社SaaS開発', '2025-04-01');
        $createCompanyData('合同会社WebWorks', 'free_trial', $teacher1, 'ECサイト制作', null);
        // Added for Yamada
        $createCompanyData('サイバーゲート・システムズ', 'active', $teacher1, 'セキュリティ診断ツール開発', '2025-12-10');
        $createCompanyData('株式会社AppMedia', 'active', $teacher1, 'スマホアプリ運営・開発', '2026-01-05');
        $createCompanyData('スタートアップX', 'free_trial', $teacher1, '新規事業MVP開発', null);
        $createCompanyData('レガシーバンク', 'finished', $teacher1, '基幹システムマイグレーション', '2025-05-15');
        $createCompanyData('ゲームスタジオPixel', 'active', $teacher1, 'ソーシャルゲーム開発', '2025-11-01');


        // Teacher 2 Companies (Suzuki - Keep as is)
        $createCompanyData('未来通信株式会社', 'active', $teacher2, '通信インフラ、AI解析', '2026-01-15');
        $createCompanyData('AIソリューションズ', 'active', $teacher2, '画像認識AI導入', '2025-12-01');
        $createCompanyData('データサイエンス研究所', 'finished', $teacher2, 'データ分析基盤構築', '2025-08-01');

        // Teacher 3 Companies (Tanaka - DX/Consulting)
        $createCompanyData('グローバル商事', 'active', $teacher3, '総合商社、業務DX', '2025-11-20');
        $createCompanyData('昭和製造株式会社', 'finished', $teacher3, '部品製造、在庫管理DX', '2025-06-01');
        $createCompanyData('鈴木商店', 'free_trial', $teacher3, '小売、POSレジ導入検討', null);
        // Added for Tanaka
        $createCompanyData('建設スマート株式会社', 'active', $teacher3, '建設現場管理DX', '2025-12-15');
        $createCompanyData('メディケア・プラス', 'active', $teacher3, '病院予約システム導入', '2026-01-10');
        $createCompanyData('アグリテック・フィールズ', 'free_trial', $teacher3, '農業IoTデータ活用', null);
        $createCompanyData('都市開発公社', 'finished', $teacher3, '行政手続きオンライン化支援', '2025-07-20');
        $createCompanyData('リテールチェーンA', 'active', $teacher3, '全店POSデータ統合', '2025-10-10');


        // 4. Lecture Pages
        LecturePage::create([
            'title' => '第1回：AI基礎概論',
            'description' => "AI（人工知能）の歴史、定義、そして現代社会における役割について学びます。\n\n【この講義のゴール】\n・AIの基本的な仕組みを理解する\n・機械学習とディープラーニングの違いを説明できる",
            'sort_order' => 1,
        ]);

        LecturePage::create([
            'title' => '第2回：プロンプトエンジニアリング実践',
            'description' => "生成AIに対する効果的な指示（プロンプト）の出し方を習得します。\n\n【課題】\n指定されたシナリオに基づいて、適切なプロンプトを作成し、その出力結果をスクリーンショットで提出してください。",
            'sort_order' => 2,
        ]);

        LecturePage::create([
            'title' => '第3回：ビジネス活用事例と倫理',
            'description' => "実際のビジネス現場でのAI活用事例を分析し、同時に著作権やプライバシーなどの倫理的課題について考えます。",
            'sort_order' => 3,
        ]);

        LecturePage::create([
            'title' => '第4回：ノーコードAI開発入門',
            'description' => "プログラミング知識がなくてもAIアプリを作成できる「ノーコードツール」の使い方を学びます。",
            'sort_order' => 4,
        ]);
    }
}
